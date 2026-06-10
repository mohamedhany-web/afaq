<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientChangeRequest;
use App\Models\User;
use App\Services\Crm\ClientTimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientApprovalService
{
    public function __construct(
        protected ClientManagementService $clients,
        protected CrmRecordApprovalService $approval,
    ) {}

    public function requiresApproval(User $user): bool
    {
        return $this->approval->requiresApproval($user);
    }

    public function canApprove(User $user): bool
    {
        return $this->approval->canApproveClients($user);
    }

    public function submitCreate(Request $request, User $user): ClientChangeRequest
    {
        $data = $this->clients->prepareData(
            $this->clients->validate($request),
            $user,
            true
        );

        $change = ClientChangeRequest::create([
            'action' => ClientChangeRequest::ACTION_CREATE,
            'status' => ClientChangeRequest::STATUS_PENDING,
            'requested_by' => $user->id,
            'summary' => 'طلب إضافة: ' . ($data['name'] ?? 'عميل جديد'),
            'payload' => ['client' => $data],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function submitUpdate(Request $request, Client $client, User $user): ClientChangeRequest
    {
        $this->assertNoPendingConflict($client, ClientChangeRequest::ACTION_UPDATE);

        $data = $this->clients->prepareData(
            $this->clients->validate($request),
            $user,
            false
        );

        $change = ClientChangeRequest::create([
            'action' => ClientChangeRequest::ACTION_UPDATE,
            'status' => ClientChangeRequest::STATUS_PENDING,
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'summary' => 'طلب تعديل: ' . $client->name,
            'payload' => ['client' => $data],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function submitDelete(Client $client, User $user, string $reason): ClientChangeRequest
    {
        if (!$this->clients->canDelete($user, $client)) {
            abort(403);
        }

        $this->assertNoPendingConflict($client, ClientChangeRequest::ACTION_DELETE);

        $change = ClientChangeRequest::create([
            'action' => ClientChangeRequest::ACTION_DELETE,
            'status' => ClientChangeRequest::STATUS_PENDING,
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'request_reason' => $reason,
            'summary' => 'طلب حذف: ' . $client->name,
            'payload' => [
                'client_name' => $client->name,
                'delete_reason' => $reason,
            ],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function approve(ClientChangeRequest $change, User $reviewer, ?string $notes = null): ?Client
    {
        if (!$this->canApprove($reviewer)) {
            abort(403);
        }

        if ((int) $change->requested_by === (int) $reviewer->id) {
            abort(403, 'لا يمكنك الموافقة على طلبك الخاص.');
        }

        if ($change->status !== ClientChangeRequest::STATUS_PENDING) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً.');
        }

        return DB::transaction(function () use ($change, $reviewer, $notes) {
            $client = match ($change->action) {
                ClientChangeRequest::ACTION_CREATE => $this->applyCreate($change, $reviewer),
                ClientChangeRequest::ACTION_UPDATE => $this->applyUpdate($change, $reviewer),
                ClientChangeRequest::ACTION_DELETE => $this->applyDelete($change, $reviewer),
                default => abort(422, 'نوع طلب غير مدعوم'),
            };

            $change->update([
                'status' => ClientChangeRequest::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
                'client_id' => $client?->id ?? $change->client_id,
            ]);

            CrmNotificationService::notify(
                $change->requested_by,
                'client_change_approved',
                'تمت الموافقة على طلب العميل',
                $change->summary . ' — تم التنفيذ.',
                ['request_id' => $change->id, 'client_id' => $client?->id],
            );

            return $client;
        });
    }

    public function reject(ClientChangeRequest $change, User $reviewer, ?string $notes = null): void
    {
        if (!$this->canApprove($reviewer)) {
            abort(403);
        }

        if ($change->status !== ClientChangeRequest::STATUS_PENDING) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً.');
        }

        $change->update([
            'status' => ClientChangeRequest::STATUS_REJECTED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        CrmNotificationService::notify(
            $change->requested_by,
            'client_change_rejected',
            'تم رفض طلب العميل',
            ($notes ? $notes . ' — ' : '') . $change->summary,
            ['request_id' => $change->id],
        );
    }

    public function pendingForClient(Client $client): ?ClientChangeRequest
    {
        return ClientChangeRequest::query()
            ->where('client_id', $client->id)
            ->where('status', ClientChangeRequest::STATUS_PENDING)
            ->latest()
            ->first();
    }

    protected function applyCreate(ClientChangeRequest $change, User $reviewer): Client
    {
        return $this->clients->createFromPayload($change->payload ?? [], $reviewer);
    }

    protected function applyUpdate(ClientChangeRequest $change, User $reviewer): Client
    {
        $client = Client::findOrFail($change->client_id);
        $client->update(($change->payload ?? [])['client'] ?? []);

        return $client->fresh();
    }

    protected function applyDelete(ClientChangeRequest $change, User $reviewer): ?Client
    {
        $client = Client::findOrFail($change->client_id);
        $this->clients->deleteClient($client);

        return null;
    }

    protected function assertNoPendingConflict(Client $client, string $action): void
    {
        $exists = ClientChangeRequest::query()
            ->where('client_id', $client->id)
            ->where('status', ClientChangeRequest::STATUS_PENDING)
            ->whereIn('action', [$action, ClientChangeRequest::ACTION_DELETE])
            ->exists();

        if ($exists) {
            abort(422, 'يوجد طلب معلّق على هذا العميل — انتظر موافقة الإدارة.');
        }
    }

    protected function notifyApprovers(ClientChangeRequest $change): void
    {
        $approvers = User::permission('approve-client-changes')->get()
            ->merge(User::role(['super_admin', 'admin'])->get())
            ->unique('id');

        foreach ($approvers as $admin) {
            CrmNotificationService::notify(
                $admin->id,
                'client_change_pending',
                'طلب عميل بانتظار الموافقة',
                $change->summary . ' — من ' . ($change->requester?->name ?? 'موظف'),
                [
                    'request_id' => $change->id,
                    'url' => route('crm.clients.approvals.show', $change),
                ],
                'client_change_pending:' . $change->id,
            );
        }
    }
}
