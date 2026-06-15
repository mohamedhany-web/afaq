<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContract extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'employee_id',
        'contract_number',
        'title',
        'contract_type',
        'start_date',
        'end_date',
        'salary',
        'status',
        'terms',
        'file_path',
        'original_filename',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return config("hr_contracts.types.{$this->contract_type}", $this->contract_type);
    }

    public function statusLabel(): string
    {
        return config("hr_contracts.status_labels.{$this->status}", $this->status);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->end_date
            && $this->end_date->lte(now()->addDays($days))
            && $this->end_date->gte(now());
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $prefix = 'EMP-CNT-' . $year . '-';

        $last = self::where('contract_number', 'like', $prefix . '%')
            ->orderByDesc('contract_number')
            ->value('contract_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
