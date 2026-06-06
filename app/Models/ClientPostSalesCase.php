<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPostSalesCase extends Model
{
    protected $fillable = [
        'client_id',
        'sale_id',
        'project_id',
        'assigned_to',
        'created_by',
        'case_type',
        'status',
        'department',
        'title',
        'description',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return config('crm_intelligence.post_sales_types')[$this->case_type] ?? $this->case_type;
    }

    public function statusLabel(): string
    {
        return config('crm_intelligence.post_sales_statuses')[$this->status] ?? $this->status;
    }
}
