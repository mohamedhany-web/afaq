<?php

namespace App\Services\Freelance;

use App\Models\Compensation\CompCommissionPlan;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\FreelanceAgentContract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FreelanceAgentContractService
{
    public function validate(Request $request, ?FreelanceAgentContract $contract = null): array
    {
        return Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'contract_number' => 'nullable|string|max:64',
            'national_id' => 'nullable|string|max:32',
            'nationality' => 'nullable|string|max:64',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => ['required', Rule::in(array_keys(config('freelance_agents.contract_statuses')))],
            'quarterly_target_amount' => 'nullable|numeric|min:0',
            'quarterly_target_deals' => 'nullable|integer|min:1',
            'company_signatory_name' => 'nullable|string|max:120',
            'company_signatory_title' => 'nullable|string|max:120',
            'signed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ])->validate();
    }

    public function create(array $data, User $creator): FreelanceAgentContract
    {
        return DB::transaction(function () use ($data, $creator) {
            $contract = FreelanceAgentContract::create(array_merge($data, [
                'created_by' => $creator->id,
                'signed_at' => $data['signed_at'] ?? now(),
            ]));

            $this->ensureFreelanceProfile((int) $data['user_id']);

            return $contract->fresh(['user']);
        });
    }

    public function update(FreelanceAgentContract $contract, array $data): FreelanceAgentContract
    {
        $contract->update($data);
        $this->ensureFreelanceProfile((int) $contract->user_id);

        return $contract->fresh(['user']);
    }

    protected function ensureFreelanceProfile(int $userId): void
    {
        $plan = CompCommissionPlan::query()
            ->where('model', 'freelance_scheme')
            ->where('is_active', true)
            ->first();

        CompEmployeeProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'base_salary' => 0,
                'commission_plan_id' => $plan?->id,
                'is_active' => true,
                'effective_from' => now()->startOfMonth()->toDateString(),
                'meta' => ['agent_type' => 'freelance'],
            ],
        );
    }
}
