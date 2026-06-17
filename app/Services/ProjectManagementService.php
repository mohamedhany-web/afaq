<?php

namespace App\Services;

use App\Helpers\MapLocationHelper;
use App\Models\Project;
use App\Models\ProjectMapPin;
use App\Models\RealEstateDeveloper;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectManagementService
{
    public function __construct(protected ProjectPolicy $policy) {}

    public function canViewAny(User $user): bool
    {
        return $this->policy->viewAny($user);
    }

    public function canView(User $user, Project $project): bool
    {
        return $this->policy->view($user, $project);
    }

    public function canCreate(User $user): bool
    {
        return $this->policy->create($user);
    }

    public function canUpdate(User $user, Project $project): bool
    {
        return $this->policy->update($user, $project);
    }

    public function canDelete(User $user, Project $project): bool
    {
        return $this->policy->delete($user, $project);
    }

    public function scopedQuery(User $user)
    {
        $query = Project::query();

        if ($user->can('view-all-projects')) {
            return $query;
        }

        if ($user->can('view-own-projects')) {
            $query->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereHas('teamMembers', fn ($team) => $team->where('user_id', $user->id));
            });
        }

        return $query->whereRaw('0 = 1');
    }

    public function mergeMapPayload(Request $request): void
    {
        if ($request->filled('map_pins_payload')) {
            $decoded = json_decode($request->input('map_pins_payload'), true);
            $request->merge(['map_pins' => is_array($decoded) ? $decoded : []]);
        }
    }

    public function validate(Request $request, ?Project $project = null): array
    {
        $this->mergeMapPayload($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'developer_name' => 'nullable|string|max:255',
            'real_estate_developer_id' => 'nullable|exists:real_estate_developers,id',
            'ownership_type' => ['required', Rule::in(array_keys(Project::OWNERSHIP_TYPES))],
            'ownership_details' => 'nullable|array',
            'city' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'land_area_m2' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'map_zoom' => 'nullable|integer|between:3,20',
            'property_types' => 'required|array|min:1',
            'property_types.*' => ['required', Rule::in(array_keys(Project::PROPERTY_TYPES))],
            'project_type' => ['nullable', Rule::in(array_keys(Project::DEVELOPMENT_TYPES))],
            'listing_status' => ['required', Rule::in(array_keys(Project::LISTING_STATUSES))],
            'total_units' => 'nullable|integer|min:0',
            'available_units' => 'nullable|integer|min:0',
            'sold_units' => 'nullable|integer|min:0',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'project_manager_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'map_pins' => 'nullable|array',
            'map_pins.*.title' => 'required_with:map_pins|string|max:120',
            'map_pins.*.pin_type' => 'nullable|in:project,unit,landmark,entrance',
            'map_pins.*.latitude' => 'required_with:map_pins|numeric|between:-90,90',
            'map_pins.*.longitude' => 'required_with:map_pins|numeric|between:-180,180',
            'map_pins.*.notes' => 'nullable|string|max:500',
        ]);

        $validator->after(function ($v) use ($request) {
            $type = $request->input('ownership_type');
            $details = $request->input('ownership_details', []);

            if ($type === 'partnership' && empty($details['partner_name'])) {
                $v->errors()->add('ownership_details.partner_name', 'اسم الشريك مطلوب في مشاريع المشاركة.');
            }

            if (in_array(Project::normalizeOwnershipType($type), Project::OWNERSHIP_REQUIRES_DEVELOPER, true)) {
                if (!$request->filled('real_estate_developer_id')) {
                    $v->errors()->add('real_estate_developer_id', 'اختر مطوراً عقارياً مسجلاً بتعاقد نشط من لوحة الإدارة.');
                } elseif (!RealEstateDeveloper::contracted()->whereKey($request->input('real_estate_developer_id'))->exists()) {
                    $v->errors()->add('real_estate_developer_id', 'المطور المحدد غير مسجل أو التعاقد غير نشط.');
                }
            }
        });

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    public function normalize(array $data, Request $request, User $user): array
    {
        $total = (int) ($data['total_units'] ?? 0);
        $sold = (int) ($data['sold_units'] ?? 0);

        if ($request->filled('available_units')) {
            $data['available_units'] = (int) $data['available_units'];
        } else {
            $data['available_units'] = max(0, $total - $sold);
        }

        $data['sold_units'] = $sold;
        $data['total_units'] = $total;
        $data['project_manager_id'] = $data['project_manager_id'] ?? $user->id;
        $data['status'] = 'in_progress';
        $data['priority'] = 'medium';
        $data['progress_percentage'] = $total > 0 ? (int) round(($sold / $total) * 100) : 0;
        $data['start_date'] = $data['start_date'] ?? now()->toDateString();
        $data['map_zoom'] = $data['map_zoom'] ?? 14;

        $lat = isset($data['latitude']) && $data['latitude'] !== '' && $data['latitude'] !== null
            ? (float) $data['latitude'] : null;
        $lng = isset($data['longitude']) && $data['longitude'] !== '' && $data['longitude'] !== null
            ? (float) $data['longitude'] : null;
        $pins = $request->input('map_pins', []);
        $hasProjectPin = is_array($pins) && collect($pins)->contains(
            fn ($pin) => ($pin['pin_type'] ?? '') === 'project'
        );

        if (!$hasProjectPin && MapLocationHelper::isPlaceholder($lat, $lng)) {
            $data['latitude'] = null;
            $data['longitude'] = null;
        }

        $ownershipType = Project::normalizeOwnershipType($data['ownership_type'] ?? '') ?? 'developer';

        if (in_array($ownershipType, Project::OWNERSHIP_REQUIRES_DEVELOPER, true)) {
            $data['ownership_details'] = null;
        } else {
            $data['ownership_details'] = $this->filterOwnershipDetails(
                $ownershipType,
                $data['ownership_details'] ?? []
            );
        }

        $data['ownership_type'] = $ownershipType;

        $propertyTypes = Project::normalizePropertyTypes($data['property_types'] ?? []);
        $data['property_types'] = $propertyTypes;
        $data['property_type'] = $propertyTypes[0] ?? null;

        unset($data['map_pins']);

        return $data;
    }

    public function resolveDeveloper(array $data, User $user): array
    {
        $ownershipType = Project::normalizeOwnershipType($data['ownership_type'] ?? '') ?? 'developer';

        if (!in_array($ownershipType, Project::OWNERSHIP_REQUIRES_DEVELOPER, true)) {
            $data['real_estate_developer_id'] = null;
            $data['developer_name'] = null;

            return $data;
        }

        $developer = !empty($data['real_estate_developer_id'])
            ? RealEstateDeveloper::find($data['real_estate_developer_id'])
            : null;

        $data['developer_name'] = $developer?->name;
        $data['real_estate_developer_id'] = $developer?->id;

        return $data;
    }

    public function filterOwnershipDetails(string $type, array $details): array
    {
        $allowed = Project::OWNERSHIP_DETAIL_FIELDS[$type] ?? [];
        $filtered = [];

        foreach ($allowed as $key) {
            if (!array_key_exists($key, $details)) {
                continue;
            }
            $value = $details[$key];
            if ($value === null || $value === '') {
                continue;
            }
            $filtered[$key] = $value;
        }

        return $filtered;
    }

    public function deleteProject(Project $project, User $user): void
    {
        if (!$this->canDelete($user, $project)) {
            abort(403, 'لا تملك صلاحية حذف هذا المشروع.');
        }

        if (!$project->isDeletable()) {
            abort(422, 'لا يمكن حذف مشروع مرتبط بصفقات أو يحتوي على وحدات مباعة.');
        }

        $project->teamMembers()->detach();
        $project->delete();
    }

    public function ownershipStats($query): array
    {
        $counts = (clone $query)
            ->selectRaw('ownership_type, COUNT(*) as total, COALESCE(SUM(available_units), 0) as units')
            ->groupBy('ownership_type')
            ->get()
            ->keyBy('ownership_type');

        return collect(Project::OWNERSHIP_TYPES)->map(function ($label, $key) use ($counts) {
            $row = $counts->get($key);

            return [
                'key' => $key,
                'label' => $label,
                'count' => (int) ($row->total ?? 0),
                'units' => (int) ($row->units ?? 0),
            ];
        })->values()->all();
    }

    public function syncMapPins(Project $project, Request $request, User $user): void
    {
        $pins = $request->input('map_pins', []);

        if (!is_array($pins)) {
            $pins = [];
        }

        $project->mapPins()->delete();

        foreach ($pins as $pin) {
            if (empty($pin['latitude']) || empty($pin['longitude']) || empty($pin['title'])) {
                continue;
            }

            $project->mapPins()->create([
                'title' => $pin['title'],
                'pin_type' => $pin['pin_type'] ?? 'unit',
                'latitude' => $pin['latitude'],
                'longitude' => $pin['longitude'],
                'notes' => $pin['notes'] ?? null,
                'created_by' => $user->id,
            ]);
        }

        $main = collect($pins)->firstWhere('pin_type', 'project') ?? ($pins[0] ?? null);

        if ($main && !empty($main['latitude']) && !empty($main['longitude'])) {
            $lat = (float) $main['latitude'];
            $lng = (float) $main['longitude'];
            if (!MapLocationHelper::isPlaceholder($lat, $lng)) {
                $project->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);
            }
        } elseif (empty($pins) && MapLocationHelper::isPlaceholder(
            $project->latitude !== null ? (float) $project->latitude : null,
            $project->longitude !== null ? (float) $project->longitude : null,
        )) {
            $project->update(['latitude' => null, 'longitude' => null]);
        }
    }

    public function formUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('name')->get();
    }
}
