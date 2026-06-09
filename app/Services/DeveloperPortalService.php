<?php

namespace App\Services;

use App\Models\DeveloperAccount;
use App\Models\Project;
use App\Models\RealEstateDeveloper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeveloperPortalService
{
    public function developer(DeveloperAccount $account): RealEstateDeveloper
    {
        return $account->developer;
    }

    public function projectsQuery(DeveloperAccount $account): Builder
    {
        return Project::query()
            ->where('real_estate_developer_id', $account->real_estate_developer_id)
            ->where('ownership_type', 'developer_third_party');
    }

    public function canAccessProject(DeveloperAccount $account, Project $project): bool
    {
        return (int) $project->real_estate_developer_id === (int) $account->real_estate_developer_id
            && $project->ownership_type === 'developer_third_party';
    }

    /** @return array<string, mixed> */
    public function validateProject(Request $request, ?Project $project = null): array
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'land_area_m2' => 'nullable|numeric|min:0',
            'property_type' => ['required', Rule::in(array_keys(Project::PROPERTY_TYPES))],
            'project_type' => ['nullable', Rule::in(array_keys(Project::DEVELOPMENT_TYPES))],
            'listing_status' => ['required', Rule::in(array_keys(Project::LISTING_STATUSES))],
            'total_units' => 'nullable|integer|min:0',
            'available_units' => 'nullable|integer|min:0',
            'sold_units' => 'nullable|integer|min:0',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ])->validate();
    }

    /** @param array<string, mixed> $data */
    public function normalizeProject(array $data): array
    {
        $total = (int) ($data['total_units'] ?? 0);
        $sold = (int) ($data['sold_units'] ?? 0);
        $data['available_units'] = max(0, $total - $sold);
        $data['sold_units'] = $sold;
        $data['total_units'] = $total;
        $data['status'] = 'in_progress';
        $data['priority'] = 'medium';
        $data['progress_percentage'] = $total > 0 ? (int) round(($sold / $total) * 100) : 0;
        $data['start_date'] = $data['start_date'] ?? now()->toDateString();

        return $data;
    }

    /** @param array<string, mixed> $data */
    public function attachDeveloperMeta(array $data, RealEstateDeveloper $developer): array
    {
        $data['ownership_type'] = 'developer_third_party';
        $data['real_estate_developer_id'] = $developer->id;
        $data['developer_name'] = $developer->name;
        $data['ownership_details'] = null;

        return $data;
    }
}
