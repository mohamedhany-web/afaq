<?php

namespace App\Services;

use App\Models\DeveloperAccount;
use App\Models\DeveloperContract;
use App\Models\RealEstateDeveloper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeveloperManagementService
{
    /** @return array<string, mixed> */
    public function validateDeveloper(Request $request, ?RealEstateDeveloper $developer = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('real_estate_developers', 'name')->ignore($developer?->id)],
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => ['required', Rule::in(array_keys(RealEstateDeveloper::STATUSES))],
            'portal_enabled' => 'nullable|boolean',
            'contract_ref' => 'nullable|string|max:120',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'exclusivity' => 'nullable|boolean',
            'exclusivity_until' => 'nullable|date',
            'contact_person' => 'nullable|string|max:120',
            'contact_phone' => 'nullable|string|max:40',
            'listing_terms' => 'nullable|string',
            'contract_notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_status' => ['nullable', Rule::in(array_keys(DeveloperContract::STATUSES))],
            'portal_account_name' => 'nullable|string|max:255',
            'portal_account_email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('developer_accounts', 'email')->ignore(
                    $developer?->accounts()->where('email', $request->input('portal_account_email'))->value('id')
                ),
            ],
            'portal_account_password' => 'nullable|string|min:8|confirmed',
            'portal_account_role' => ['nullable', Rule::in(array_keys(DeveloperAccount::ROLES))],
        ]);

        $validator->after(function ($v) use ($request, $developer) {
            if (!$request->boolean('portal_enabled')) {
                return;
            }

            $hasAccount = $developer?->accounts()->exists() ?? false;

            if (!$request->filled('portal_account_email')) {
                $v->errors()->add('portal_account_email', 'البريد مطلوب لتفعيل بوابة المطور.');
            }

            if ($request->filled('portal_account_email') && !$hasAccount && !$request->filled('portal_account_password')) {
                $v->errors()->add('portal_account_password', 'كلمة المرور مطلوبة لإنشاء حساب البوابة.');
            }
        });

        return $validator->validate();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, User $user): RealEstateDeveloper
    {
        return DB::transaction(function () use ($data, $user) {
            $developer = RealEstateDeveloper::create([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'portal_enabled' => (bool) ($data['portal_enabled'] ?? false),
                'created_by' => $user->id,
            ]);

            $this->syncContract($developer, $data, $user, true);
            $this->syncPortalAccount($developer, $data, true);

            return $developer->fresh(['activeContract', 'accounts']);
        });
    }

    /** @param array<string, mixed> $data */
    public function update(RealEstateDeveloper $developer, array $data, User $user): RealEstateDeveloper
    {
        return DB::transaction(function () use ($developer, $data, $user) {
            $developer->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'portal_enabled' => (bool) ($data['portal_enabled'] ?? false),
            ]);

            $this->syncContract($developer, $data, $user, false);
            $this->syncPortalAccount($developer, $data, false);

            return $developer->fresh(['activeContract', 'accounts']);
        });
    }

    /** @param array<string, mixed> $data */
    protected function syncContract(RealEstateDeveloper $developer, array $data, User $user, bool $isNew): void
    {
        $contract = $developer->activeContract;

        $payload = [
            'contract_ref' => $data['contract_ref'] ?? null,
            'commission_percent' => $data['commission_percent'] ?? null,
            'exclusivity' => (bool) ($data['exclusivity'] ?? false),
            'exclusivity_until' => $data['exclusivity_until'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'listing_terms' => $data['listing_terms'] ?? null,
            'notes' => $data['contract_notes'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['contract_status'] ?? DeveloperContract::STATUS_ACTIVE,
            'approved_at' => ($data['contract_status'] ?? DeveloperContract::STATUS_ACTIVE) === DeveloperContract::STATUS_ACTIVE ? now() : null,
        ];

        if ($contract) {
            $contract->update($payload);

            return;
        }

        DeveloperContract::create(array_merge($payload, [
            'real_estate_developer_id' => $developer->id,
            'created_by' => $user->id,
        ]));
    }

    /** @param array<string, mixed> $data */
    protected function syncPortalAccount(RealEstateDeveloper $developer, array $data, bool $isNew): void
    {
        if (!$developer->portal_enabled || empty($data['portal_account_email'])) {
            return;
        }

        $existing = $developer->accounts()->where('email', $data['portal_account_email'])->first();

        if ($existing) {
            $existing->update([
                'name' => $data['portal_account_name'] ?? $existing->name,
                'portal_role' => $data['portal_account_role'] ?? $existing->portal_role,
                'is_active' => true,
            ]);
            if (!empty($data['portal_account_password'])) {
                $existing->update(['password' => $data['portal_account_password']]);
            }

            return;
        }

        DeveloperAccount::create([
            'real_estate_developer_id' => $developer->id,
            'name' => $data['portal_account_name'] ?? $developer->name,
            'email' => $data['portal_account_email'],
            'password' => $data['portal_account_password'],
            'portal_role' => $data['portal_account_role'] ?? DeveloperAccount::ROLE_OWNER,
            'is_active' => true,
        ]);
    }
}
