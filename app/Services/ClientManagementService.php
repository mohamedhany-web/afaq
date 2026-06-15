<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Services\Crm\ClientTimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function validate(Request $request): array
    {
        $type = Client::normalizeType($request->input('client_type', 'individual'));

        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'id_number' => $type === 'freelance' ? 'required|string|max:50' : 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'client_type' => 'nullable|in:' . implode(',', Client::typeKeys()),
            'lead_source' => 'nullable|in:' . implode(',', Client::leadSourceKeys()),
            'status' => 'required|in:active,inactive,suspended,prospect',
        ], [
            'id_number.required' => 'رقم البطاقة إلزامي لعملاء فري لانس.',
        ])->validate();
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
            $data['lead_stage'] = 'lead';
        }

        return $data;
    }

    public function createFromPayload(array $payload, User $actor): Client
    {
        $client = Client::create($payload['client'] ?? []);

        app(ClientTimelineService::class)->recordLeadCreated($client, $actor);

        return $client;
    }

    public function deleteClient(Client $client): void
    {
        if ($client->projects()->count() > 0 || $client->sales()->count() > 0) {
            abort(422, 'لا يمكن حذف العميل لأنه مرتبط بصفقات أو مشاريع');
        }

        $client->delete();
    }
}
