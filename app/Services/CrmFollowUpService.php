<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
class CrmFollowUpService
{
    public function __construct(protected CrmScopeService $scope) {}

    public static function for(User $user): self
    {
        return new self(CrmScopeService::for($user));
    }

    public function followUpsQuery(): Builder
    {
        $query = CrmFollowUp::query();
        $userId = $this->scope->user()->id;

        if ($this->scope->hasFullAccess()) {
            return $query;
        }

        if ($this->scope->isManagerScope()) {
            $memberIds = $this->scope->managedTeamMemberUserIds();

            return $query->where(function (Builder $q) use ($memberIds, $userId) {
                $q->whereIn('user_id', $memberIds)
                    ->orWhere('created_by', $userId);
            });
        }

        return $query->where(function (Builder $q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('created_by', $userId);
        });
    }

    public function create(array $data, User $actor): CrmFollowUp
    {
        $client = Client::findOrFail($data['client_id']);
        $this->scope->assertClientInScope((int) $client->id);

        $assigneeId = (int) ($data['user_id'] ?? $actor->id);
        $this->assertCanAssignTo($actor, $assigneeId);

        $saleId = $data['sale_id'] ?? null;
        if ($saleId) {
            $sale = $this->scope->salesQuery()->where('id', $saleId)->where('client_id', $client->id)->first();
            if (!$sale) {
                abort(422, 'الصفقة غير متاحة لهذا العميل.');
            }
        }

        $scheduledAt = Carbon::parse($data['scheduled_at']);

        $followUp = CrmFollowUp::create([
            'user_id' => $assigneeId,
            'created_by' => $actor->id,
            'client_id' => $client->id,
            'sale_id' => $saleId,
            'interaction_type' => $data['interaction_type'],
            'notes' => $data['notes'],
            'scheduled_at' => $scheduledAt,
            'status' => CrmFollowUp::STATUS_SCHEDULED,
        ]);

        $this->dispatchNotifications($followUp, $actor);

        $this->appendClientNotes($client, $followUp);

        if ($followUp->interaction_type === 'viewing' && $saleId) {
            Sale::where('id', $saleId)->update([
                'viewing_date' => $scheduledAt->toDateString(),
                'viewing_notes' => $data['notes'],
            ]);
        }

        return $followUp->load(['client', 'user', 'creator', 'sale']);
    }

    public function complete(CrmFollowUp $followUp, User $actor): CrmFollowUp
    {
        $this->authorizeFollowUp($followUp);

        $followUp->update([
            'status' => CrmFollowUp::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return $followUp;
    }

    public function cancel(CrmFollowUp $followUp, User $actor): CrmFollowUp
    {
        $this->authorizeFollowUp($followUp);

        $followUp->update(['status' => CrmFollowUp::STATUS_CANCELLED]);

        return $followUp;
    }

    public function authorizeFollowUp(CrmFollowUp $followUp): void
    {
        if (!$this->followUpsQuery()->where('id', $followUp->id)->exists()) {
            abort(403, 'لا يمكنك الوصول إلى هذه المتابعة.');
        }
    }

    protected function assertCanAssignTo(User $actor, int $assigneeId): void
    {
        $scope = CrmScopeService::for($actor);

        if ($scope->hasFullAccess()) {
            return;
        }

        if ($scope->isManagerScope()) {
            if (!in_array($assigneeId, $scope->managedTeamMemberUserIds(), true)) {
                abort(403, 'لا يمكنك تعيين المتابعة لهذا الموظف.');
            }

            return;
        }

        if ((int) $assigneeId !== (int) $actor->id) {
            abort(403, 'يمكنك جدولة المتابعات لنفسك فقط.');
        }
    }

    protected function dispatchNotifications(CrmFollowUp $followUp, User $actor): void
    {
        $followUp->load(['client', 'user', 'creator']);
        $typeLabel = $followUp->typeLabel();
        $when = $followUp->scheduled_at->format('Y/m/d H:i');
        $data = [
            'url' => route('crm.follow-ups.index', ['date' => $followUp->scheduled_at->toDateString(), 'highlight' => $followUp->id]),
            'follow_up_id' => $followUp->id,
            'client_id' => $followUp->client_id,
        ];

        $message = "{$typeLabel} مع العميل {$followUp->client->name} — {$when}";

        $assigneeMessage = (int) $followUp->user_id === (int) $actor->id
            ? "تم تسجيل موعد: {$message}"
            : "جدول لك {$actor->name}: {$message}";

        CrmNotificationService::notifyFollowUpScheduled($followUp->user, [
            'message' => $assigneeMessage,
            'data' => $data,
        ]);

        if (!CrmScopeService::for($actor)->isManagerScope()) {
            $this->notifyTeamManagers($followUp, $actor, $typeLabel, $when, $data);
        }
    }

    protected function notifyTeamManagers(CrmFollowUp $followUp, User $actor, string $typeLabel, string $when, array $data): void
    {
        if (CrmScopeService::for($actor)->isManagerScope()) {
            return;
        }

        $teamIds = \Illuminate\Support\Facades\DB::table('sales_team_members')
            ->where('user_id', $actor->id)
            ->pluck('sales_team_id');

        $managerIds = SalesTeam::whereIn('id', $teamIds)
            ->pluck('manager_id')
            ->unique()
            ->filter(fn ($id) => $id && (int) $id !== (int) $actor->id);

        foreach ($managerIds as $managerId) {
            CrmNotificationService::notifyManagerOfTeamActivity(User::find($managerId), [
                'title' => 'متابعة جديدة من الفريق',
                'message' => "{$actor->name}: {$typeLabel} — {$followUp->client->name} — {$when}",
                'data' => $data,
            ]);
        }
    }

    protected function appendClientNotes(Client $client, CrmFollowUp $followUp): void
    {
        $entry = "--- {$followUp->scheduled_at->format('Y-m-d H:i')} | {$followUp->typeLabel()} ---\n{$followUp->notes}";
        $client->update([
            'notes' => trim(($client->notes ? $client->notes . "\n\n" : '') . $entry),
        ]);
    }

    /** @return User[] */
    public function assignableUsers(User $actor): array
    {
        $scope = CrmScopeService::for($actor);

        if ($scope->hasFullAccess()) {
            return User::role(array_merge(
                CrmEmployeeService::LEGACY_MANAGER_ROLES,
                CrmEmployeeService::LEGACY_EMPLOYEE_ROLES
            ))->orderBy('name')->get()->all();
        }

        if ($scope->isManagerScope()) {
            return User::whereIn('id', $scope->managedTeamMemberUserIds())->orderBy('name')->get()->all();
        }

        return [$actor];
    }
}
