<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'scheduled_check_in_at',
        'late_minutes',
        'check_out',
        'break_start',
        'break_end',
        'break_duration_minutes',
        'current_status',
        'total_hours',
        'overtime_hours',
        'status',
        'notes',
        'check_in_location',
        'check_out_location',
        'required_hours',
        'scheduled_checkout_at',
        'auto_checkout',
        'work_day_locked',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'scheduled_check_in_at' => 'datetime',
        'late_minutes' => 'integer',
        'check_out' => 'datetime',
        'scheduled_checkout_at' => 'datetime',
        'auto_checkout' => 'boolean',
        'work_day_locked' => 'boolean',
        'required_hours' => 'decimal:1',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'break_duration_minutes' => 'integer',
        'current_status' => 'string',
    ];

    /**
     * Get the employee that owns the attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present' => 'green',
            'absent' => 'red',
            'late' => 'yellow',
            'half_day' => 'orange',
            'leave' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Calculate total working hours.
     */
    public function calculateTotalHours(): int
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = \Carbon\Carbon::parse($this->check_out);
        
        $totalMinutes = $checkOut->diffInMinutes($checkIn);
        
        // Subtract break time if exists
        if ($this->break_start && $this->break_end) {
            $breakStart = \Carbon\Carbon::parse($this->break_start);
            $breakEnd = \Carbon\Carbon::parse($this->break_end);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }
        
        return round($totalMinutes / 60, 2);
    }

    /**
     * Check if employee is late.
     */
    public function getIsLateAttribute(): bool
    {
        if (!$this->check_in) {
            return false;
        }
        
        $checkInTime = \Carbon\Carbon::parse($this->check_in);
        $expectedTime = \Carbon\Carbon::parse('09:00'); // Default start time
        
        return $checkInTime->gt($expectedTime);
    }

    /**
     * Scope for current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    /**
     * Scope for specific employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope for specific user_id.
     * This allows queries like: Attendance::whereUserId($userId)->first()
     */
    public function scopeWhereUserId($query, $userId)
    {
        return $query->whereHas('employee', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Create a new Eloquent query builder for the model.
     * This allows intercepting where('user_id', ...) calls and converting them to whereHas('employee', ...)
     */
    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                // If column is 'user_id', convert it to whereHas('employee', ...)
                if ($column === 'user_id') {
                    // Determine the actual value based on number of arguments
                    $actualValue = $value;
                    if (func_num_args() === 2) {
                        $actualValue = $operator;
                    } elseif (func_num_args() === 3) {
                        $actualValue = $value;
                    }
                    
                    return $this->whereHas('employee', function($q) use ($actualValue) {
                        $q->where('user_id', $actualValue);
                    }, $boolean === 'and' ? 'and' : 'or');
                }
                
                return parent::where($column, $operator, $value, $boolean);
            }
        };
    }
}
