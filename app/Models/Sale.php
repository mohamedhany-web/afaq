<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_source',
        'client_id',
        'assigned_to',
        'product_service',
        'estimated_value',
        'actual_value',
        'stage',
        'lost_reason',
        'lost_reason_notes',
        'lost_at',
        'probability_percentage',
        'expected_close_date',
        'actual_close_date',
        'notes',
        'competitors',
        'decision_makers',
        'project_id',
        'sales_team_id',
        'unit_type',
        'interest_type',
        'viewing_date',
        'viewing_notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'lost_at' => 'datetime',
        'viewing_date' => 'date',
        'competitors' => 'array',
        'decision_makers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * القيمة المعروضة (فعلية أو متوقعة) — للتوافق مع واجهات تستخدم amount.
     */
    public function getAmountAttribute(): float
    {
        return (float) ($this->actual_value ?? $this->estimated_value ?? 0);
    }

    public static function sumAmount(?callable $scope = null): float
    {
        $query = static::query();

        if ($scope) {
            $scope($query);
        }

        return (float) ($query
            ->selectRaw('COALESCE(SUM(COALESCE(actual_value, estimated_value)), 0) as total')
            ->value('total') ?? 0);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function salesRep(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to', 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function salesTeam(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class);
    }
}
