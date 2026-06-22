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
            'inventory_source' => ['required', Rule::in(array_keys(Project::inventorySourceLabels()))],
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
            'classification_pricing' => 'nullable|array',
            'classification_pricing.*.price_from' => 'nullable|numeric|min:0',
            'classification_pricing.*.price_to' => 'nullable|numeric|min:0',
            'classification_pricing.*.area_from' => 'nullable|numeric|min:0',
            'classification_pricing.*.area_to' => 'nullable|numeric|min:0',
            'classification_pricing.*.building_percent' => 'nullable|numeric|min:0|max:100',
            'classification_pricing.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'classification_pricing.*.loading_percent' => 'nullable|numeric|min:0|max:100',
            'classification_pricing.*.maintenance_deposit' => 'nullable|numeric|min:0',
            'classification_pricing.*.default_down_percent' => 'nullable|numeric|min:0|max:100',
            'classification_pricing.*.default_installment_years' => 'nullable|integer|min:0|max:40',
            'manual_units' => 'nullable|array|max:500',
            'manual_units.*.area_m2' => 'nullable|numeric|min:0',
            'manual_units.*.use_type' => ['nullable', Rule::in(array_keys(config('project_units.use_types', [])))],
            'manual_units.*.direction' => ['nullable', Rule::in(array_keys(config('project_inventory.directions', [])))],
            'manual_units.*.floor_number' => 'nullable|string|max:16',
            'manual_units.*.floor_label' => 'nullable|string|max:64',
            'manual_units.*.apartment_number' => 'nullable|string|max:32',
            'manual_units.*.unit_price_total' => 'nullable|numeric|min:0',
            'manual_units.*.building_percent' => 'nullable|numeric|min:0|max:100',
            'manual_units.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'manual_units.*.loading_percent' => 'nullable|numeric|min:0|max:100',
            'manual_units.*.maintenance_deposit' => 'nullable|numeric|min:0',
            'manual_units.*.down_percent' => 'nullable|numeric|min:0|max:100',
            'manual_units.*.years' => 'nullable|integer|min:0|max:40',
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

            $pricing = $request->input('classification_pricing', []);
            if (is_array($pricing)) {
                foreach ($pricing as $key => $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $from = $row['price_from'] ?? null;
                    $to = $row['price_to'] ?? null;
                    if ($from !== null && $from !== '' && $to !== null && $to !== '' && (float) $to < (float) $from) {
                        $label = Project::CLASSIFICATION_TYPES[$key] ?? $key;
                        $v->errors()->add("classification_pricing.{$key}.price_to", "سعر «إلى» لـ {$label} يجب أن يكون أكبر من أو يساوي «من».");
                    }
                    $areaFrom = $row['area_from'] ?? null;
                    $areaTo = $row['area_to'] ?? null;
                    if ($areaFrom !== null && $areaFrom !== '' && $areaTo !== null && $areaTo !== '' && (float) $areaTo < (float) $areaFrom) {
                        $label = Project::CLASSIFICATION_TYPES[$key] ?? $key;
                        $v->errors()->add("classification_pricing.{$key}.area_to", "مساحة «إلى» لـ {$label} يجب أن تكون أكبر من أو تساوي «من».");
                    }
                }
            }
        });

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }

    public function normalize(array $data, Request $request, User $user, ?Project $project = null): array
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
        $inventorySource = Project::normalizeInventorySource($data['inventory_source'] ?? null)
            ?? match ($ownershipType) {
                'afaq_private' => 'company',
                'developer' => 'developer',
                default => 'non_company',
            };

        $data['inventory_source'] = $inventorySource;
        $data['ownership_type'] = match ($inventorySource) {
            'company' => 'afaq_private',
            'developer' => 'developer',
            default => Project::normalizeOwnershipType($data['ownership_type'] ?? '') ?? 'direct_owner',
        };
        $ownershipType = $data['ownership_type'];

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

        $pricing = $this->normalizeClassificationPricing(
            $request->input('classification_pricing', []),
            $propertyTypes,
        );
        $buildingConfig = is_array($project?->building_config) ? $project->building_config : [];
        $buildingConfig['classification_pricing'] = $pricing;
        $data['building_config'] = $buildingConfig;

        $priceFromValues = collect($pricing)->pluck('price_from')->filter(fn ($v) => $v !== null && $v !== '');
        $priceToValues = collect($pricing)->pluck('price_to')->filter(fn ($v) => $v !== null && $v !== '');
        if ($priceFromValues->isNotEmpty()) {
            $data['price_from'] = $priceFromValues->min();
        }
        if ($priceToValues->isNotEmpty()) {
            $data['price_to'] = $priceToValues->max();
        }

        unset($data['map_pins'], $data['classification_pricing'], $data['manual_units']);

        return $data;
    }

    /** @param  list<array<string, mixed>>  $rows */
    public function syncManualUnits(Project $project, array $rows): int
    {
        if (! $project->usesManualUnits()) {
            return 0;
        }

        return app(ProjectManualUnitService::class)->sync($project, $rows, replace: true);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, RealEstateDeveloper> */
    public function contractedDevelopers()
    {
        return RealEstateDeveloper::contracted()
            ->orderBy('name')
            ->get(['id', 'name', 'city']);
    }

    /** @param  array<string, mixed>  $raw
     * @param  list<string>  $propertyTypes
     * @return array<string, array{price_from?: float, price_to?: float, area_from?: float, area_to?: float}>
     */
    public function normalizeClassificationPricing(array $raw, array $propertyTypes): array
    {
        $allowed = in_array('mixed', $propertyTypes, true)
            ? Project::concreteClassificationKeys()
            : array_values(array_intersect($propertyTypes, Project::concreteClassificationKeys()));

        if ($allowed === []) {
            $allowed = Project::concreteClassificationKeys();
        }

        $normalized = [];
        foreach ($allowed as $key) {
            $row = is_array($raw[$key] ?? null) ? $raw[$key] : [];
            $entry = [];
            foreach (['price_from', 'price_to', 'area_from', 'area_to', 'building_percent', 'discount_percent', 'loading_percent', 'maintenance_deposit', 'default_down_percent'] as $field) {
                $val = $row[$field] ?? null;
                if ($val !== null && $val !== '') {
                    $entry[$field] = in_array($field, ['default_installment_years'], true)
                        ? (int) $val
                        : (float) $val;
                }
            }
            $years = $row['default_installment_years'] ?? null;
            if ($years !== null && $years !== '') {
                $entry['default_installment_years'] = (int) $years;
            }
            if ($entry !== []) {
                $normalized[$key] = $entry;
            }
        }

        return $normalized;
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
            ->selectRaw('inventory_source, COUNT(*) as total, COALESCE(SUM(available_units), 0) as units')
            ->groupBy('inventory_source')
            ->get()
            ->keyBy('inventory_source');

        return collect(Project::inventorySourceLabels())->map(function ($label, $key) use ($counts) {
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
