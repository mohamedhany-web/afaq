<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that owns the activity log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action badge color.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'viewed' => 'gray',
            'approved' => 'green',
            'rejected' => 'red',
            'assigned' => 'yellow',
            'completed' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get action name in Arabic.
     */
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            'created' => 'تم الإنشاء',
            'updated' => 'تم التحديث',
            'deleted' => 'تم الحذف',
            'viewed' => 'تم المشاهدة',
            'approved' => 'تم الموافقة',
            'rejected' => 'تم الرفض',
            'assigned' => 'تم التعيين',
            'completed' => 'تم الإنجاز',
            'logged_in' => 'تم تسجيل الدخول',
            'logged_out' => 'تم تسجيل الخروج',
            default => $this->action
        };
    }

    /**
     * Get model name in Arabic.
     */
    public function getModelNameAttribute(): string
    {
        return match($this->model_type) {
            'App\Models\Project' => 'مشروع عقاري',
            'App\Models\Employee' => 'موظف',
            'App\Models\Client' => 'عميل',
            'App\Models\Department' => 'قسم',
            'App\Models\User' => 'مستخدم',
            'App\Models\Invoice' => 'فاتورة',
            'App\Models\Contract' => 'عقد',
            'App\Models\Ticket' => 'تذكرة',
            'App\Models\Sale' => 'بيع',
            'App\Models\Leave' => 'إجازة',
            'App\Models\Salary' => 'راتب',
            'App\Models\Attendance' => 'حضور',
            default => class_basename($this->model_type)
        };
    }

    /**
     * Get time ago in Arabic.
     */
    public function getTimeAgoAttribute(): string
    {
        $diff = $this->created_at->diffForHumans();
        
        $replacements = [
            'seconds ago' => 'منذ ثواني',
            'second ago' => 'منذ ثانية',
            'minutes ago' => 'منذ دقائق',
            'minute ago' => 'منذ دقيقة',
            'hours ago' => 'منذ ساعات',
            'hour ago' => 'منذ ساعة',
            'days ago' => 'منذ أيام',
            'day ago' => 'منذ يوم',
            'weeks ago' => 'منذ أسابيع',
            'week ago' => 'منذ أسبوع',
            'months ago' => 'منذ أشهر',
            'month ago' => 'منذ شهر',
            'years ago' => 'منذ سنوات',
            'year ago' => 'منذ سنة',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $diff);
    }

    /**
     * Get formatted description.
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $description = $this->description;
        
        // Add model name if available
        if ($this->model_type && $this->model_id) {
            $modelName = $this->model_name;
            $description = str_replace('{model}', $modelName, $description);
        }
        
        return $description;
    }

    /**
     * Scope for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific model.
     */
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope for specific action.
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for recent activities.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's activities.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
