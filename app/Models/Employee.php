<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'reports_to_user_id',
        'first_name',
        'last_name',
        'email',
        'position',
        'phone',
        'national_id',
        'address',
        'emergency_contact',
        'emergency_phone',
        'hire_date',
        'salary',
        'daily_hours',
        'work_start_time',
        'work_end_time',
        'weekly_off_days',
        'late_grace_minutes',
        'employment_type',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'daily_hours' => 'decimal:1',
        'weekly_off_days' => 'array',
        'late_grace_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reports_to_user_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'assigned_to', 'user_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'assigned_to');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function custodyAssignments(): HasMany
    {
        return $this->hasMany(CustodyAssignment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function adminNotes(): HasMany
    {
        return $this->hasMany(EmployeeAdminNote::class);
    }

    /**
     * توليد رقم توظيفي تلقائي
     */
    public static function generateEmployeeId($prefix = null, $length = null)
    {
        $prefix = $prefix ?? \App\Helpers\SettingsHelper::getEmployeeIdPrefix();
        $length = $length ?? \App\Helpers\SettingsHelper::getEmployeeIdLength();
        
        do {
            // توليد رقم عشوائي
            $number = str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $employeeId = $prefix . $number;
            
            // التحقق من عدم وجود الرقم في قاعدة البيانات
        } while (static::where('employee_id', $employeeId)->exists());
        
        return $employeeId;
    }

    /**
     * توليد رقم توظيفي متسلسل (أعلى رقم مستخدم + 1، مع تجنّب التكرار)
     */
    public static function generateSequentialEmployeeId($prefix = null)
    {
        $prefix = $prefix ?? \App\Helpers\SettingsHelper::getEmployeeIdPrefix();
        $length = (int) \App\Helpers\SettingsHelper::getEmployeeIdLength();

        $maxNumber = static::where('employee_id', 'like', $prefix . '%')
            ->pluck('employee_id')
            ->map(function (string $id) use ($prefix) {
                $suffix = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $id);

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        $next = $maxNumber;

        do {
            $next++;
            $employeeId = $prefix . str_pad((string) $next, $length, '0', STR_PAD_LEFT);
        } while (static::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    /**
     * توليد رقم توظيفي حسب الإعدادات
     */
    public static function generateEmployeeIdBySettings()
    {
        $type = \App\Helpers\SettingsHelper::getEmployeeIdType();
        
        if ($type === 'random') {
            return static::generateEmployeeId();
        } else {
            return static::generateSequentialEmployeeId();
        }
    }
}
