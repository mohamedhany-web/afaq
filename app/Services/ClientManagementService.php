<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDeletionBatch;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Services\Crm\ClientActivityService;
use App\Services\Crm\ClientTimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class ClientManagementService
{
    public function __construct(protected ClientPolicy $policy) {}

    public function canCreate(User $user): bool
    {
        return $this->policy->create($user);
    }

    public function canUpdate(User $user, Client $client): bool
    {
        return $this->policy->update($user, $client);
    }

    public function canDelete(User $user, Client $client): bool
    {
        return $this->policy->delete($user, $client);
    }

    public function validate(Request $request, ?Client $client = null): array
    {
        $type = Client::normalizeType($request->input('client_type', 'individual'));

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'id_number' => $type === 'freelance' ? 'required|string|max:50' : 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'description' => 'nullable|string|max:5000',
            'client_type' => 'nullable|in:' . implode(',', Client::typeKeys()),
            'lead_source' => 'nullable|in:' . implode(',', Client::leadSourceKeys()),
            'lead_source_details' => 'nullable|array',
            'lead_source_details.referrer_name' => 'nullable|string|max:255',
            'lead_source_details.event_name' => 'nullable|string|max:255',
            'lead_source_details.campaign_name' => 'nullable|string|max:255',
            'lead_source_details.broker_name' => 'nullable|string|max:255',
            'lead_source_details.broker_id_number' => 'nullable|string|max:50',
            'marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
            'status' => 'required|in:active,inactive,suspended,prospect',
        ], [
            'id_number.required' => 'رقم البطاقة إلزامي لعملاء فري لانس.',
        ]);

        $validator->after(function (ValidationValidator $v) use ($client) {
            $phone = $v->getData()['phone'] ?? null;
            if (!$phone || $v->errors()->has('phone')) {
                return;
            }

            $duplicate = Client::findByNormalizedPhone($phone, $client?->id);
            if ($duplicate) {
                $duplicate->loadMissing('assignedEmployee');
                $v->errors()->add('phone', Client::duplicatePhoneMessage($duplicate));
            }

            $source = Client::normalizeLeadSource($v->getData()['lead_source'] ?? null);
            $details = $v->getData()['lead_source_details'] ?? [];
            if (! is_array($details)) {
                $details = [];
            }

            if (! $source) {
                return;
            }

            if ($source === 'marketing') {
                $campaignId = $v->getData()['marketing_campaign_id'] ?? null;
                $campaignName = trim((string) ($details['campaign_name'] ?? ''));
                if (! $campaignId && $campaignName === '') {
                    $v->errors()->add('lead_source_details.campaign_name', 'حدّد حملة تسويقية أو اكتب اسم/نوع الحملة.');
                }

                return;
            }

            foreach (Client::leadSourceDetailFields($source) as $field => $label) {
                if ($field === 'broker_id_number') {
                    continue;
                }
                if (trim((string) ($details[$field] ?? '')) === '') {
                    $v->errors()->add("lead_source_details.{$field}", "حقل «{$label}» مطلوب عند اختيار هذا المصدر.");
                }
            }

            if ($source === 'broker' && trim((string) ($details['broker_id_number'] ?? '')) === '') {
                $v->errors()->add('lead_source_details.broker_id_number', 'رقم البطاقة/الهوية مطلوب عند اختيار مصدر بروكر.');
            }
        });

        return $validator->validate();
    }

    public function prepareData(array $data, User $user, bool $isCreate = false): array
    {
        if (isset($data['company'])) {
            $data['company_name'] = $data['company'];
            unset($data['company']);
        }

        $data['client_type'] = Client::normalizeType($data['client_type'] ?? 'individual');

        if (array_key_exists('lead_source', $data)) {
            $data['lead_source'] = filled($data['lead_source'])
                ? Client::normalizeLeadSource($data['lead_source'])
                : null;
        }

        $data['lead_source_details'] = $this->normalizeLeadSourceDetails(
            $data['lead_source'] ?? null,
            $data['lead_source_details'] ?? [],
        );

        if (($data['lead_source'] ?? null) !== 'marketing') {
            $data['marketing_campaign_id'] = null;
        }

        if ($isCreate) {
            $scope = CrmScopeService::for($user);
            $requested = isset($data['assigned_to']) ? (int) $data['assigned_to'] : null;
            $allowed = $scope->assignableEmployeeIds();

            if ($user->canAccessOperations() && !$requested) {
                $data['assigned_to'] = null;
            } elseif ($requested && in_array($requested, $allowed, true)) {
                $data['assigned_to'] = $requested;
            } else {
                $data['assigned_to'] = $user->employee?->id;
            }

            $data['created_by'] = $user->id;
            $data['lead_stage'] = CrmScopeService::LEAD_STAGE_NEW;
        }

        return $data;
    }

    public function createFromPayload(array $payload, User $actor): Client
    {
        $data = $payload['client'] ?? [];
        Client::assertUniquePhone($data['phone'] ?? null);

        $client = Client::create($data);

        app(ClientTimelineService::class)->recordLeadCreated($client, $actor);

        return $client;
    }

    public function deleteClient(Client $client, User $user, string $reason, ?\Illuminate\Http\Request $request = null, ?ClientDeletionBatch $batch = null): void
    {
        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            abort(422, 'لا يمكن حذف العميل لأنه مرتبط بصفقات أو مشاريع');
        }

        app(ClientActivityService::class)->logDeleted($client, $user, $reason, $batch, $request);

        $client->delete();
    }

    /** @return list<array{id: int, name: string, phone: string|null}> */
    public function snapshotClients(iterable $clients): array
    {
        $snapshots = [];
        foreach ($clients as $client) {
            $snapshots[] = app(ClientActivityService::class)->clientSnapshot($client);
        }

        return $snapshots;
    }

    /** @param  array<string, mixed>  $raw
     * @return array<string, string>|null
     */
    public function normalizeLeadSourceDetails(?string $source, array $raw): ?array
    {
        $source = Client::normalizeLeadSource($source);
        if (! $source) {
            return null;
        }

        $allowed = array_keys(Client::leadSourceDetailFields($source));
        if ($allowed === []) {
            return null;
        }

        $normalized = [];
        foreach ($allowed as $key) {
            $value = trim((string) ($raw[$key] ?? ''));
            if ($value !== '') {
                $normalized[$key] = $value;
            }
        }

        return $normalized === [] ? null : $normalized;
    }
}
