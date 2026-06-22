<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    public const CLASSIFICATION_TYPES = [
        'residential' => 'سكني',
        'administrative' => 'إداري',
        'commercial' => 'تجاري',
        'medical' => 'طبي',
        'mixed' => 'مختلط',
    ];

    public const LEGACY_PROPERTY_TYPES = [
        'land' => 'أراضي',
        'villa' => 'فلل',
    ];

    public const PROPERTY_TYPES = self::CLASSIFICATION_TYPES + self::LEGACY_PROPERTY_TYPES;

    public const DEVELOPMENT_TYPES = [
        'compound' => 'كمبوند',
        'tower' => 'برج',
        'villas' => 'مجمع فلل',
        'land_plot' => 'أرض مخططة',
        'commercial_center' => 'مركز تجاري',
    ];

    public const LISTING_STATUSES = [
        'upcoming' => 'قريباً',
        'active' => 'متاح للبيع',
        'sold_out' => 'نفدت الوحدات',
        'completed' => 'مكتمل',
    ];

    public const OWNERSHIP_TYPES = [
        'direct_owner' => 'مالك مباشر',
        'trader' => 'تاجر',
        'broker' => 'وسيط',
        'investor' => 'مستثمر',
        'developer' => 'مطور',
        'afaq_private' => 'خاص بأفاق',
        'partnership' => 'مشاركات',
        'property_management' => 'إدارة ممتلكات',
    ];

    /** قيم قديمة → التصنيف الجديد */
    public const LEGACY_OWNERSHIP_TYPES = [
        'owned' => 'afaq_private',
        'developer_third_party' => 'developer',
    ];

    public const OWNERSHIP_REQUIRES_DEVELOPER = ['developer'];

    public const INVENTORY_SOURCES = [
        'company' => 'وحدات الشركة',
        'non_company' => 'وحدات الغير',
        'developer' => 'مشاريع المطورين',
    ];

    public static function inventorySourceLabels(): array
    {
        return config('project_inventory.sources', self::INVENTORY_SOURCES);
    }

    public static function normalizeInventorySource(?string $source): ?string
    {
        $source = $source ? strtolower(trim($source)) : null;

        return array_key_exists($source, self::inventorySourceLabels()) ? $source : null;
    }

    public function inventorySourceLabel(): string
    {
        return self::inventorySourceLabels()[$this->inventory_source] ?? ($this->inventory_source ?: '—');
    }

    public function usesManualUnits(): bool
    {
        return in_array($this->inventory_source, ['company', 'non_company'], true);
    }

    public function usesDeveloperTables(): bool
    {
        return ($this->inventory_source ?? 'developer') === 'developer';
    }

    /** @return list<string> */
    public function developerTableKeys(): array
    {
        return array_keys(config('project_inventory.developer_tables', []));
    }

    public const OWNERSHIP_DETAIL_FIELDS = [
        'direct_owner' => ['contact_name', 'contact_phone', 'contract_ref', 'notes'],
        'trader' => ['contact_name', 'contact_phone', 'commission_percent', 'contract_ref', 'notes'],
        'broker' => ['contact_name', 'contact_phone', 'commission_percent', 'contract_ref', 'notes'],
        'investor' => ['contact_name', 'contact_phone', 'investment_amount', 'share_percent', 'notes'],
        'developer' => [],
        'afaq_private' => ['internal_entity', 'acquisition_date', 'investment_amount', 'management_notes'],
        'partnership' => ['partner_name', 'partner_phone', 'partner_contact', 'our_share_percent', 'partner_share_percent', 'contract_ref', 'partnership_start', 'partnership_notes'],
        'property_management' => ['contact_name', 'contact_phone', 'fee_percent', 'contract_ref', 'notes'],
    ];

    protected $fillable = [
        'name',
        'description',
        'client_id',
        'department_id',
        'project_manager_id',
        'start_date',
        'end_date',
        'budget',
        'status',
        'priority',
        'progress_percentage',
        'team_members',
        'project_type',
        'location',
        'latitude',
        'longitude',
        'map_zoom',
        'city',
        'property_type',
        'property_types',
        'total_units',
        'available_units',
        'sold_units',
        'price_from',
        'price_to',
        'developer_name',
        'real_estate_developer_id',
        'ownership_type',
        'inventory_source',
        'ownership_details',
        'listing_status',
        'land_area_m2',
        'building_config',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'map_zoom' => 'integer',
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
        'team_members' => 'array',
        'total_units' => 'integer',
        'available_units' => 'integer',
        'sold_units' => 'integer',
        'ownership_details' => 'array',
        'property_types' => 'array',
        'land_area_m2' => 'decimal:2',
        'building_config' => 'array',
    ];

    /**
     * Get the client that owns the project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function realEstateDeveloper(): BelongsTo
    {
        return $this->belongsTo(RealEstateDeveloper::class);
    }

    /**
     * Get the department that owns the project.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the project manager for the project.
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }
    
    /**
     * Get team members as an alias for teamMembers.
     */
    public function team(): BelongsToMany
    {
        return $this->teamMembers();
    }

    /**
     * Get the invoices for the project.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the contracts for the project.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get team members users.
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_team_members', 'project_id', 'user_id');
    }

    /**
     * Get project status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get project status name in Arabic.
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'planning' => 'قيد التخطيط',
            'in_progress' => 'قيد التنفيذ',
            'on_hold' => 'متوقف',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => $this->status
        };
    }

    /** @return list<string> */
    public static function normalizePropertyTypes(mixed $value): array
    {
        $allowed = array_keys(self::PROPERTY_TYPES);

        if (is_array($value)) {
            return array_values(array_unique(array_intersect(
                array_map('strval', $value),
                $allowed
            )));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $trimmed = trim($value);
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);

            return is_array($decoded) ? self::normalizePropertyTypes($decoded) : [];
        }

        if (str_contains($trimmed, ',')) {
            return self::normalizePropertyTypes(explode(',', $trimmed));
        }

        return in_array($trimmed, $allowed, true) ? [$trimmed] : [];
    }

    /** @return list<string> */
    public function resolvedPropertyTypes(): array
    {
        $fromJson = self::normalizePropertyTypes($this->property_types);

        if ($fromJson !== []) {
            return $fromJson;
        }

        return self::normalizePropertyTypes($this->property_type);
    }

    public function getPropertyTypeNameAttribute(): string
    {
        $types = $this->resolvedPropertyTypes();

        if ($types === []) {
            return '—';
        }

        return collect($types)
            ->map(fn (string $key) => self::PROPERTY_TYPES[$key] ?? $key)
            ->implode('، ');
    }

    public static function formatPropertyTypesLabel(mixed $value): string
    {
        $types = self::normalizePropertyTypes($value);

        if ($types === []) {
            return '—';
        }

        return collect($types)
            ->map(fn (string $key) => self::PROPERTY_TYPES[$key] ?? $key)
            ->implode('، ');
    }

    /** @return list<string> */
    public static function concreteClassificationKeys(): array
    {
        return array_keys(config('project_classifications.concrete', []));
    }

    /** @return array<string, array{price_from?: float, price_to?: float, area_from?: float, area_to?: float}> */
    public function classificationPricing(): array
    {
        $pricing = $this->building_config['classification_pricing'] ?? [];

        return is_array($pricing) ? $pricing : [];
    }

    public function classificationLabel(string $key): string
    {
        return config('project_classifications.types.' . $key)
            ?? config('project_classifications.concrete.' . $key)
            ?? self::PROPERTY_TYPES[$key]
            ?? $key;
    }

    /** تصنيفات المشروع القابلة للعرض في الفلتر */
    public function activeClassifications(): array
    {
        $types = $this->resolvedPropertyTypes();
        $concrete = self::concreteClassificationKeys();
        $pricing = $this->classificationPricing();

        if (in_array('mixed', $types, true)) {
            $candidates = $concrete;
        } else {
            $candidates = array_values(array_intersect($types, $concrete));
        }

        if ($candidates === []) {
            $candidates = $concrete;
        }

        $unitTypes = $this->relationLoaded('units')
            ? $this->units->pluck('use_type')->unique()->all()
            : $this->units()->distinct()->pluck('use_type')->all();

        return array_values(array_filter($candidates, function (string $key) use ($pricing, $unitTypes) {
            if (in_array($key, $unitTypes, true)) {
                return true;
            }
            $row = $pricing[$key] ?? [];

            if (! empty($row['price_from']) || ! empty($row['price_to'])
                || ! empty($row['area_from']) || ! empty($row['area_to'])) {
                return true;
            }

            return empty($pricing)
                && ($this->price_from || $this->price_to)
                && in_array($key, $this->resolvedPropertyTypes(), true);
        }));
    }

    /** @return array{price_from: ?float, price_to: ?float, area_from: ?float, area_to: ?float, label: string}|null */
    public function classificationSummary(string $key): ?array
    {
        if (! in_array($key, self::concreteClassificationKeys(), true)) {
            return null;
        }

        $stored = $this->classificationPricing()[$key] ?? [];
        $units = $this->relationLoaded('units')
            ? $this->units->where('use_type', $key)
            : $this->units()->where('use_type', $key)->get();

        $areaFrom = isset($stored['area_from']) && $stored['area_from'] !== ''
            ? (float) $stored['area_from'] : null;
        $areaTo = isset($stored['area_to']) && $stored['area_to'] !== ''
            ? (float) $stored['area_to'] : null;

        if ($units->isNotEmpty()) {
            $minArea = (float) $units->min('area_m2');
            $maxArea = (float) $units->max('area_m2');
            $areaFrom = $areaFrom ?? $minArea;
            $areaTo = $areaTo ?? $maxArea;
        }

        $priceFrom = isset($stored['price_from']) && $stored['price_from'] !== ''
            ? (float) $stored['price_from'] : null;
        $priceTo = isset($stored['price_to']) && $stored['price_to'] !== ''
            ? (float) $stored['price_to'] : null;

        if ($units->isNotEmpty()) {
            $cashPrices = $units->pluck('price_cash')->filter(fn ($p) => $p > 0);
            $instPrices = $units->pluck('price_installment')->filter(fn ($p) => $p > 0);
            $allPrices = $cashPrices->merge($instPrices);
            if ($allPrices->isNotEmpty()) {
                $priceFrom = $priceFrom ?? (float) $allPrices->min();
                $priceTo = $priceTo ?? (float) $allPrices->max();
            }
        }

        if ($priceFrom === null && $priceTo === null && ($this->price_from || $this->price_to)) {
            $types = $this->resolvedPropertyTypes();
            if (in_array($key, $types, true)) {
                $priceFrom = $this->price_from ? (float) $this->price_from : null;
                $priceTo = $this->price_to ? (float) $this->price_to : null;
            }
        }

        if ($priceFrom === null && $priceTo === null && $areaFrom === null && $areaTo === null) {
            return null;
        }

        return [
            'label' => $this->classificationLabel($key),
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'area_from' => $areaFrom,
            'area_to' => $areaTo,
        ];
    }

    public function getDevelopmentTypeNameAttribute(): string
    {
        return self::DEVELOPMENT_TYPES[$this->project_type] ?? ($this->project_type ?: '—');
    }

    public function getListingStatusNameAttribute(): string
    {
        return self::LISTING_STATUSES[$this->listing_status] ?? ($this->listing_status ?: '—');
    }

    public function getOwnershipTypeNameAttribute(): string
    {
        $type = self::normalizeOwnershipType($this->ownership_type);

        return self::OWNERSHIP_TYPES[$type] ?? ($this->ownership_type ?: '—');
    }

    public static function normalizeOwnershipType(?string $type): ?string
    {
        if ($type === null || $type === '') {
            return null;
        }

        return self::LEGACY_OWNERSHIP_TYPES[$type] ?? $type;
    }

    public function requiresRegisteredDeveloper(): bool
    {
        return in_array(
            self::normalizeOwnershipType($this->ownership_type),
            self::OWNERSHIP_REQUIRES_DEVELOPER,
            true
        );
    }

    public function ownershipDetail(string $key, mixed $default = null): mixed
    {
        return data_get($this->ownership_details ?? [], $key, $default);
    }

    public function displayDeveloperName(): string
    {
        return $this->realEstateDeveloper?->name
            ?? $this->developer_name
            ?? '—';
    }

    public function scopeOwnershipType($query, string $type)
    {
        return $query->where('ownership_type', $type);
    }

    public function getOccupancyPercentAttribute(): int
    {
        if (!$this->total_units || $this->total_units <= 0) {
            return 0;
        }

        $sold = $this->sold_units ?? 0;

        return (int) min(100, round(($sold / $this->total_units) * 100));
    }

    public function syncAvailableUnits(): void
    {
        $total = (int) ($this->total_units ?? 0);
        $sold = (int) ($this->sold_units ?? 0);
        $this->available_units = max(0, $total - $sold);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function mapPins(): HasMany
    {
        return $this->hasMany(ProjectMapPin::class)->orderBy('pin_type')->orderBy('title');
    }

    public function buildingFloors(): HasMany
    {
        return $this->hasMany(BuildingFloor::class)->orderBy('sort_order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class);
    }

    public function scene3d(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Project3dScene::class);
    }

    public function hasGeneratedUnits(): bool
    {
        return $this->units()->exists();
    }

    public function hasMapLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * يُسمح بحذف المشروع فقط إذا لم تُربط به صفقات أو وحدات مباعة.
     */
    public function isDeletable(): bool
    {
        if ($this->relationLoaded('sales') && $this->sales->isNotEmpty()) {
            return false;
        }

        if ($this->sales()->exists()) {
            return false;
        }

        if ((int) ($this->sold_units ?? 0) > 0) {
            return false;
        }

        if ($this->units()->where('status', ProjectUnit::STATUS_SOLD)->exists()) {
            return false;
        }

        if ($this->invoices()->exists() || $this->contracts()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    /**
     * مشاريع معروضة للبيع (حالة العرض).
     */
    public function scopeListed($query)
    {
        return $query->whereIn('listing_status', ['upcoming', 'active']);
    }

    /**
     * Scope for active projects (legacy PM status).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planning', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}