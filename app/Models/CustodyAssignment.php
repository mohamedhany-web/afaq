<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustodyAssignment extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';

    protected $fillable = [
        'employee_id',
        'asset_id',
        'item_name',
        'category',
        'serial_number',
        'issued_at',
        'issued_by',
        'issue_condition',
        'issue_notes',
        'issue_file_path',
        'returned_at',
        'returned_by',
        'return_condition',
        'return_notes',
        'return_file_path',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function categoryLabel(): string
    {
        return config("custody.categories.{$this->category}", $this->category);
    }

    public function statusLabel(): string
    {
        return config("custody.status_labels.{$this->status}", $this->status);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
