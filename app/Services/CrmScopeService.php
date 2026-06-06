<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CrmScopeService
{
    public const LEAD_STAGES = ['lead', 'prospect', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];

    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function user(): User
    {
        return $this->user;
    }

    public function hasFullAccess(): bool
    {
        return $this->user->hasRole(['super_admin', 'admin']);
    }

    public function isManagerScope(): bool
    {
        if ($this->hasFullAccess()) {
            return false;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_MANAGER_ROLES)
            || $this->user->managedSalesTeams()->exists();
    }

    public function isRepScope(): bool
    {
        if ($this->hasFullAccess() || $this->isManagerScope()) {
            return false;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES);
    }

    /** @return int[] */
    public function adminUserIds(): array
    {
        return Cache::remember('crm_admin_user_ids', 300, function () {
            return User::role(['super_admin', 'admin'])->pluck('id')->all();
        });
    }

    public function salesQuery(): Builder
    {
        $query = Sale::query();

        if ($this->hasFullAccess()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            $memberIds = $this->managedTeamMemberUserIds();

            return $query
                ->whereIn('assigned_to', $memberIds)
                ->whereHas('client', fn ($c) => $this->applyClientVisibility($c, false));
        }

        return $query
            ->where('assigned_to', $this->user->id)
            ->whereHas('client', fn ($c) => $this->applyClientVisibility($c, false));
    }

    public function clientsQuery(): Builder
    {
        return $this->applyClientVisibility(Client::query(), true);
    }

    protected function applyClientVisibility(Builder $query, bool $includeSalesLink): Builder
    {
        if ($this->hasFullAccess()) {
            return $query;
        }

        $adminIds = $this->adminUserIds();

        if ($this->isManagerScope()) {
            $memberUserIds = $this->managedTeamMemberUserIds();
            $memberEmployeeIds = Employee::whereIn('user_id', $memberUserIds)->pluck('id');

            $query->where(function ($q) use ($memberUserIds, $memberEmployeeIds, $includeSalesLink) {
                $q->whereIn('created_by', $memberUserIds)
                    ->orWhere(function ($q2) use ($memberEmployeeIds) {
                        $q2->whereNull('created_by')->whereIn('assigned_to', $memberEmployeeIds);
                    });

                if ($includeSalesLink) {
                    $q->orWhereHas('sales', fn ($s) => $s->whereIn('assigned_to', $memberUserIds));
                }
            });

            return $this->excludeAdminCreatedClients($query, $adminIds);
        }

        if ($this->isRepScope()) {
            $query->where(function ($q) use ($includeSalesLink) {
                $q->where('created_by', $this->user->id)
                    ->orWhere('assigned_to', $this->user->employee?->id);

                if ($includeSalesLink) {
                    $q->orWhereHas('sales', fn ($s) => $s->where('assigned_to', $this->user->id));
                }
            });

            return $this->excludeAdminCreatedClients($query, $adminIds);
        }

        return $query->whereRaw('1 = 0');
    }

    protected function excludeAdminCreatedClients(Builder $query, array $adminIds): Builder
    {
        if ($adminIds === []) {
            return $query;
        }

        return $query->where(function ($q) use ($adminIds) {
            $q->whereNull('created_by')
                ->orWhereNotIn('created_by', $adminIds);
        });
    }

    public function employeesQuery(): Builder
    {
        $salesDeptId = CrmEmployeeService::salesDepartment()->id;
        $query = Employee::query()->where('department_id', $salesDeptId);

        if ($this->hasFullAccess()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            return $query->whereIn('user_id', $this->managedTeamMemberUserIds());
        }

        return $query->where('user_id', $this->user->id);
    }

    public function managedTeamsQuery(): Builder
    {
        if ($this->hasFullAccess()) {
            return SalesTeam::query();
        }

        return SalesTeam::where('manager_id', $this->user->id);
    }

    /** @return int[] */
    public function managedTeamMemberUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::pluck('id')->all();
        }

        if (!$this->isManagerScope()) {
            return [$this->user->id];
        }

        $ids = collect([$this->user->id]);

        SalesTeam::where('manager_id', $this->user->id)
            ->with('members:id')
            ->get()
            ->each(function (SalesTeam $team) use ($ids) {
                $ids->push(...$team->members->pluck('id'));
            });

        return $ids->unique()->values()->all();
    }

    /** @deprecated */
    public function teamMemberUserIds(): array
    {
        return $this->managedTeamMemberUserIds();
    }

    /** @return int[] */
    public function managedTeamIds(): array
    {
        if ($this->hasFullAccess()) {
            return SalesTeam::pluck('id')->all();
        }

        return SalesTeam::where('manager_id', $this->user->id)->pluck('id')->all();
    }

    public function canViewTeamMember(User $member): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }

        if ($this->isRepScope()) {
            return (int) $member->id === (int) $this->user->id;
        }

        if ($this->isManagerScope()) {
            return in_array($member->id, $this->managedTeamMemberUserIds(), true);
        }

        return (int) $member->id === (int) $this->user->id;
    }

    public function canViewEmployee(Employee $employee): bool
    {
        if (!$employee->user_id) {
            return $this->hasFullAccess();
        }

        return $this->employeesQuery()
            ->where('id', $employee->id)
            ->exists();
    }

    public function canViewTeam(SalesTeam $team): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }

        if ((int) $team->manager_id === (int) $this->user->id) {
            return true;
        }

        return $this->isManagerScope()
            && $this->user->managedSalesTeams()->whereKey($team->id)->exists();
    }

    public function assertClientInScope(int $clientId): void
    {
        if ($this->clientsQuery()->where('id', $clientId)->exists()) {
            return;
        }

        abort(403, 'لا يمكنك الوصول إلى هذا العميل.');
    }

    public function assertSaleInScope(Sale $sale): void
    {
        if ($this->salesQuery()->where('id', $sale->id)->exists()) {
            return;
        }

        abort(403, 'لا يمكنك الوصول إلى هذه الصفقة.');
    }

    /** @return int[] */
    public function assignableRepUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')->all();
        }

        if ($this->isManagerScope()) {
            return collect($this->managedTeamMemberUserIds())
                ->filter(fn ($id) => (int) $id !== (int) $this->user->id)
                ->values()
                ->all();
        }

        return [$this->user->id];
    }

    /** مندوبو يمكن للمدير إضافتهم لفريق جديد (قسم المبيعات) */
    public function assignableTeamMemberUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')->all();
        }

        if ($this->isManagerScope()) {
            $salesDeptId = CrmEmployeeService::salesDepartment()->id;

            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->where('id', '!=', $this->user->id)
                ->whereHas('employee', fn ($q) => $q->where('department_id', $salesDeptId))
                ->pluck('id')
                ->all();
        }

        return [];
    }

    /** @return int[] */
    public function assignableEmployeeIds(): array
    {
        return $this->employeesQuery()->pluck('id')->all();
    }

    public static function creatorDisplayName(Client $client): string
    {
        $creator = $client->relationLoaded('createdBy')
            ? $client->getRelation('createdBy')
            : ($client->created_by ? $client->createdBy : null);

        if ($creator) {
            return $creator->name;
        }

        if ($client->assignedEmployee) {
            return trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) . ' (تعيين)';
        }

        return 'غير محدد';
    }

    public static function creatorIsAdmin(Client $client): bool
    {
        if (!$client->created_by) {
            return false;
        }

        $creator = $client->relationLoaded('createdBy')
            ? $client->getRelation('createdBy')
            : User::find($client->created_by);

        return $creator?->hasRole(['super_admin', 'admin']) ?? false;
    }
}
