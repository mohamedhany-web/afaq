<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'phone_normalized',
        'company_name',
        'address',
        'status',
        'lead_stage',
        'lost_reason',
        'lost_reason_notes',
        'lost_at',
        'notes',
        'description',
        'assigned_to',
        'created_by',
        'client_type',
        'lead_source',
        'lead_source_details',
        'id_number',
        'marketing_campaign_id',
    ];

    protected $casts = [
        'lost_at' => 'datetime',
        'lead_source_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ClientTimelineEvent::class)->orderByDesc('occurred_at');
    }

    public function staffNotes(): HasMany
    {
        return $this->hasMany(ClientStaffNote::class)->orderByDesc('created_at');
    }

    public function postSalesCases(): HasMany
    {
        return $this->hasMany(ClientPostSalesCase::class)->orderByDesc('created_at');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function crmTasks(): HasMany
    {
        return $this->hasMany(CrmTask::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(CrmFollowUp::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function marketingCampaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ClientAccount::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ClientNotification::class)->orderByDesc('created_at');
    }

    public function sharedDocuments(): HasMany
    {
        return $this->hasMany(ClientSharedDocument::class)->orderByDesc('created_at');
    }
    public function serviceReports(): HasMany
    {
        return $this->hasMany(ClientServiceReport::class)->orderByDesc('created_at');
    }

    public function websiteIssues(): HasMany
    {
        return $this->hasMany(ClientWebsiteIssue::class)->orderByDesc('created_at');
    }

    public function meetingRequests(): HasMany
    {
        return $this->hasMany(ClientMeetingRequest::class)->orderByDesc('created_at');
    }

    public static function typeLabels(): array
    {
        return config('client_types.labels', []);
    }

    public static function typeKeys(): array
    {
        return array_keys(static::typeLabels());
    }

    public static function normalizeType(?string $type): string
    {
        if (! $type) {
            return 'individual';
        }

        $legacy = config('client_types.legacy_map', []);

        if (isset($legacy[$type])) {
            return $legacy[$type];
        }

        return array_key_exists($type, static::typeLabels()) ? $type : 'individual';
    }

    public function typeLabel(): string
    {
        return static::typeLabels()[static::normalizeType($this->client_type)] ?? 'فرد';
    }

    public static function leadSourceLabels(): array
    {
        return config('client_lead_sources.labels', []);
    }

    /** @return array<string, array{bg: string, text: string}> */
    public static function leadSourceColors(): array
    {
        return config('client_lead_sources.colors', []);
    }

    public static function leadSourceKeys(): array
    {
        return array_keys(static::leadSourceLabels());
    }

    public static function normalizeLeadSource(?string $source): ?string
    {
        if (! $source || trim($source) === '') {
            return null;
        }

        $source = strtolower(trim($source));
        $legacy = config('client_lead_sources.legacy_map', []);

        if (isset($legacy[$source])) {
            return $legacy[$source];
        }

        if (array_key_exists($source, static::leadSourceLabels())) {
            return $source;
        }

        $byLabel = collect(static::leadSourceLabels())
            ->mapWithKeys(fn ($label, $key) => [mb_strtolower($label) => $key])
            ->all();

        return $byLabel[$source] ?? null;
    }

    public function leadSourceLabel(): ?string
    {
        $key = static::normalizeLeadSource($this->lead_source);

        return $key ? (static::leadSourceLabels()[$key] ?? $key) : null;
    }

    /** @return array<string, string> */
    public static function leadSourceDetailFields(?string $source): array
    {
        $key = static::normalizeLeadSource($source);

        return $key ? (config('client_lead_sources.detail_fields.' . $key, [])) : [];
    }

    /** @return list<array{label: string, value: string}> */
    public function leadSourceDetailLines(): array
    {
        $source = static::normalizeLeadSource($this->lead_source);
        $details = is_array($this->lead_source_details) ? $this->lead_source_details : [];
        $fields = static::leadSourceDetailFields($source);
        $lines = [];

        foreach ($fields as $key => $label) {
            $value = trim((string) ($details[$key] ?? ''));
            if ($value !== '') {
                $lines[] = ['label' => $label, 'value' => $value];
            }
        }

        if ($source === 'marketing' && $this->marketingCampaign) {
            $lines[] = ['label' => 'حملة مسجّلة', 'value' => $this->marketingCampaign->name];
        }

        return $lines;
    }

    public function assignedSalesRepName(): ?string
    {
        if (! $this->assignedEmployee) {
            return null;
        }

        return trim($this->assignedEmployee->first_name . ' ' . $this->assignedEmployee->last_name) ?: null;
    }

    public function profileUrl(string $hash = ''): string
    {
        if (auth()->user()?->can('viewFullDetails', $this)) {
            return route('crm.clients.show', $this) . $hash;
        }

        return route('crm.pipeline.client', $this) . $hash;
    }

    public static function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '966') && strlen($digits) >= 12) {
            $digits = '0' . substr($digits, 3);
        } elseif (str_starts_with($digits, '20') && strlen($digits) >= 11) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }

    public static function findByNormalizedPhone(?string $phone, ?int $ignoreId = null): ?self
    {
        $normalized = static::normalizePhone($phone);
        if (!$normalized) {
            return null;
        }

        $query = static::query()->where('phone_normalized', $normalized);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $found = $query->first();
        if ($found) {
            return $found;
        }

        return static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->lazyById(200)
            ->first(fn (self $client) => static::normalizePhone($client->phone) === $normalized);
    }

    public static function assertUniquePhone(?string $phone, ?int $ignoreId = null): void
    {
        $duplicate = static::findByNormalizedPhone($phone, $ignoreId);
        if ($duplicate) {
            abort(422, static::duplicatePhoneMessage($duplicate));
        }
    }

    public static function duplicatePhoneMessage(self $duplicate): string
    {
        $summary = $duplicate->duplicateSummary();

        return sprintf(
            'رقم الهاتف مسجّل مسبقاً للعميل «%s» — الحالة: %s · المرحلة: %s%s',
            $summary['name'],
            $summary['status_label'],
            $summary['lead_stage_label'],
            $summary['sales_rep'] ? ' · السيلز: ' . $summary['sales_rep'] : ''
        );
    }

    /** @return array<string, mixed> */
    public function duplicateSummary(): array
    {
        $stageLabels = \App\Services\CrmScopeService::leadStageLabels();
        $statusLabels = [
            'prospect' => 'محتمل',
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف',
        ];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'status' => $this->status,
            'status_label' => $statusLabels[$this->status] ?? $this->status,
            'lead_stage' => $this->lead_stage,
            'lead_stage_label' => $stageLabels[$this->lead_stage] ?? ($this->lead_stage ?: '—'),
            'lead_source' => $this->lead_source,
            'lead_source_label' => $this->leadSourceLabel(),
            'sales_rep' => $this->assignedSalesRepName(),
            'url' => route('crm.clients.show', $this),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $client) {
            if (blank($client->lead_stage)) {
                $client->lead_stage = \App\Services\CrmScopeService::LEAD_STAGE_NEW;
            }
        });

        static::saving(function (self $client) {
            if ($client->isDirty('phone')) {
                $client->phone_normalized = static::normalizePhone($client->phone);
            }
        });
    }
}
