<?php

namespace App\Services\Crm;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientDeletionBatch;
use App\Models\Employee;
use App\Models\User;
use App\Services\CrmScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ClientActivityService
{
    public const FIELD_LABELS = [
        'name' => 'الاسم',
        'phone' => 'الهاتف',
        'email' => 'البريد',
        'company_name' => 'الشركة',
        'address' => 'العنوان',
        'status' => 'الحالة',
        'lead_stage' => 'مرحلة الرحلة',
        'client_type' => 'التصنيف',
        'lead_source' => 'المصدر',
        'lead_source_details' => 'تفاصيل المصدر',
        'marketing_campaign_id' => 'حملة تسويقية',
        'assigned_to' => 'مسؤول المبيعات',
        'notes' => 'ملاحظات',
        'description' => 'وصف العميل',
        'id_number' => 'رقم البطاقة',
    ];

    public function log(
        Client $client,
        User $user,
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => Client::class,
            'model_id' => $client->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? (string) $request->userAgent() : null,
        ]);
    }

    /** @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    public function logUpdated(Client $client, User $user, array $before, array $after, ?Request $request = null): ?ActivityLog
    {
        $changes = $this->diffAttributes($before, $after);
        if ($changes === []) {
            return null;
        }

        return $this->log(
            $client,
            $user,
            'client_updated',
            'تعديل بيانات العميل: ' . $client->name,
            ['changes' => $changes],
            ['changes' => $changes],
            $request,
        );
    }

    public function logTransfer(
        Client $client,
        User $actor,
        Employee $toEmployee,
        ?Employee $fromEmployee,
        string $source,
        ?Request $request = null,
        array $tasksTransferred = [],
    ): ActivityLog {
        $toName = trim($toEmployee->first_name . ' ' . $toEmployee->last_name);
        $fromName = $fromEmployee
            ? trim($fromEmployee->first_name . ' ' . $fromEmployee->last_name)
            : null;

        $description = $fromName
            ? "تحويل العميل {$client->name} من {$fromName} إلى {$toName}"
            : "تحويل العميل {$client->name} إلى السيلز {$toName}";

        if ($tasksTransferred !== []) {
            $description .= ' — مع ' . count($tasksTransferred) . ' مهمة مرتبطة';
        }

        return $this->log(
            $client,
            $actor,
            'client_transferred',
            $description,
            [
                'assigned_to' => $fromEmployee?->id,
                'assigned_name' => $fromName,
                'assigned_user_id' => $fromEmployee?->user_id,
            ],
            [
                'assigned_to' => $toEmployee->id,
                'assigned_name' => $toName,
                'assigned_user_id' => $toEmployee->user_id,
                'transfer_source' => $source,
                'tasks_transferred' => $tasksTransferred,
                'tasks_transferred_count' => count($tasksTransferred),
            ],
            $request,
        );
    }

    /** @param  Collection<int, Client>  $clients */
    public function logBulkTransfer(
        Collection $clients,
        User $actor,
        Employee $toEmployee,
        string $source,
        ?Request $request = null,
        int $tasksTransferredTotal = 0,
    ): ActivityLog {
        $toName = trim($toEmployee->first_name . ' ' . $toEmployee->last_name);
        $ids = $clients->pluck('id')->all();
        $description = "تحويل جماعي: {$clients->count()} عميل إلى السيلز {$toName}";
        if ($tasksTransferredTotal > 0) {
            $description .= " — {$tasksTransferredTotal} مهمة مرتبطة";
        }

        return ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'client_bulk_transferred',
            'model_type' => Client::class,
            'model_id' => $clients->first()?->id,
            'description' => $description,
            'new_values' => [
                'client_ids' => $ids,
                'clients_count' => $clients->count(),
                'assigned_to' => $toEmployee->id,
                'assigned_name' => $toName,
                'assigned_user_id' => $toEmployee->user_id,
                'transfer_source' => $source,
                'tasks_transferred_count' => $tasksTransferredTotal,
                'clients' => $clients->map(fn (Client $c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'phone' => $c->phone,
                ])->values()->all(),
            ],
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? (string) $request->userAgent() : null,
        ]);
    }

    public function logDeleted(Client $client, User $user, string $reason, ?ClientDeletionBatch $batch = null, ?Request $request = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'client_deleted',
            'model_type' => Client::class,
            'model_id' => $client->id,
            'description' => 'حذف العميل: ' . $client->name,
            'old_values' => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'delete_reason' => $reason,
                'deletion_batch_id' => $batch?->id,
            ],
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? (string) $request->userAgent() : null,
        ]);
    }

    /** @param  list<array{id: int, name: string, phone: string|null}>  $snapshots */
    public function recordDeletionBatch(User $user, string $reason, array $snapshots, ?Request $request = null): ClientDeletionBatch
    {
        return ClientDeletionBatch::create([
            'user_id' => $user->id,
            'clients_count' => count($snapshots),
            'delete_reason' => $reason,
            'clients_snapshot' => $snapshots,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? (string) $request->userAgent() : null,
        ]);
    }

    public function clientSnapshot(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'phone' => $client->phone,
        ];
    }

    public function logsForClient(Client $client, int $limit = 50): Collection
    {
        return ActivityLog::query()
            ->with('user:id,name')
            ->where('model_type', Client::class)
            ->where(function ($q) use ($client) {
                $q->where('model_id', $client->id)
                    ->orWhereJsonContains('new_values->client_ids', $client->id);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /** @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return list<array{field: string, label: string, from: mixed, to: mixed}>
     */
    public function diffAttributes(array $before, array $after): array
    {
        $changes = [];
        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($keys as $key) {
            $old = $before[$key] ?? null;
            $new = $after[$key] ?? null;
            if ($old == $new) {
                continue;
            }
            $changes[] = [
                'field' => $key,
                'label' => self::FIELD_LABELS[$key] ?? $key,
                'from' => $this->formatValue($key, $old),
                'to' => $this->formatValue($key, $new),
            ];
        }

        return $changes;
    }

    protected function formatValue(string $key, mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($key === 'lead_stage') {
            return CrmScopeService::leadStageLabels()[$value] ?? $value;
        }

        if ($key === 'status') {
            return match ($value) {
                'prospect' => 'محتمل',
                'active' => 'نشط',
                'inactive' => 'غير نشط',
                'suspended' => 'موقوف',
                default => $value,
            };
        }

        return $value;
    }
}
