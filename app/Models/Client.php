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
        'company_name',
        'address',
        'status',
        'lead_stage',
        'lost_reason',
        'lost_reason_notes',
        'lost_at',
        'notes',
        'assigned_to',
        'created_by',
        'client_type',
        'lead_source',
        'marketing_campaign_id',
    ];

    protected $casts = [
        'lost_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ClientTimelineEvent::class)->orderByDesc('occurred_at');
    }

    public function postSalesCases(): HasMany
    {
        return $this->hasMany(ClientPostSalesCase::class)->orderByDesc('created_at');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
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
}
