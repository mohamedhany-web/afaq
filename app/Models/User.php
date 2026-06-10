<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\CrmEmployeeService;
use App\Services\MarketingEmployeeService;
use App\Services\OperationsEmployeeService;
use Illuminate\Contracts\Auth\Access\Gate;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the employee record associated with the user.
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function customPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Get verification codes for the user.
     */
    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    /**
     * صلاحيات Spatie / user_permissions للمفاتيح النصية.
     * فحوص السياسات (مثل can('update', $model)) تمر عبر Gate.
     */
    public function can($abilities, $arguments = [])
    {
        if (! is_array($arguments)) {
            $arguments = $arguments === null || $arguments === [] ? [] : [$arguments];
        }

        if (is_array($abilities)) {
            return app(Gate::class)->forUser($this)->check($abilities, $arguments);
        }

        if ($arguments !== []) {
            if (count($arguments) === 1 && is_string($arguments[0])) {
                return $this->checkNamedPermission($abilities, $arguments[0]);
            }

            return app(Gate::class)->forUser($this)->check($abilities, $arguments);
        }

        return $this->checkNamedPermission($abilities);
    }

    protected function checkNamedPermission(string $permissionKey, ?string $guard = null): bool
    {
        $customPermission = $this->customPermissions()
            ->where('permission_key', $permissionKey)
            ->first();

        if ($customPermission) {
            return (bool) $customPermission->is_enabled;
        }

        return $this->hasPermissionTo($permissionKey, $guard);
    }

    /**
     * Check if user has custom permission
     */
    public function hasCustomPermission($permissionKey)
    {
        // التحقق من الصلاحيات المخصصة المحفوظة أولاً
        $permission = $this->customPermissions()
                          ->where('permission_key', $permissionKey)
                          ->first();
        
        // إذا كانت الصلاحية موجودة في user_permissions، نستخدمها مباشرة
        // هذا يعني أن المستخدم قام بتعيين الصلاحية بشكل صريح (مفعّلة أو معطلة)
        if ($permission) {
            return $permission->is_enabled;
        }
        
        // إذا لم تكن موجودة، نتحقق من الصلاحيات الافتراضية للدور
        // نحصل على الصلاحيات الافتراضية من UserPermissionSeeder
        $userRoles = $this->roles->pluck('name')->toArray();
        
        // Super Admin - جميع الصلاحيات
        if (in_array('super_admin', $userRoles)) {
            return true;
        }
        
        // Admin - معظم الصلاحيات
        if (in_array('admin', $userRoles)) {
            return true;
        }
        
        // Manager - صلاحيات إدارية محدودة
        if (in_array('manager', $userRoles)) {
            $managerPermissions = [
                'sidebar_dashboard', 'sidebar_projects', 'sidebar_operations', 'sidebar_tasks',
                'sidebar_clients', 'sidebar_sales', 'sidebar_reports', 'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $managerPermissions);
        }
        
        // HR - صلاحيات موارد بشرية
        if (in_array('hr', $userRoles)) {
            $hrPermissions = [
                'sidebar_dashboard', 'sidebar_hr', 'sidebar_users', 'sidebar_employees',
                'sidebar_attendances', 'sidebar_leaves', 'sidebar_salaries', 'sidebar_reports',
                'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $hrPermissions);
        }
        
        // Employee - صلاحيات أساسية
        if (in_array('employee', $userRoles)) {
            $employeePermissions = [
                'sidebar_dashboard', 'sidebar_tasks', 'sidebar_projects_list', 'sidebar_clients',
                'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $employeePermissions);
        }
        
        // Project Manager
        if (in_array('project_manager', $userRoles)) {
            $pmPermissions = [
                'sidebar_dashboard', 'sidebar_projects', 'sidebar_tasks', 'sidebar_bugs', 'sidebar_qa',
                'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $pmPermissions);
        }
        
        // Developer
        if (in_array('developer', $userRoles)) {
            $devPermissions = [
                'sidebar_dashboard', 'sidebar_tasks', 'sidebar_projects_list', 'sidebar_bugs', 'sidebar_qa',
                'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $devPermissions);
        }
        
        // Designer
        if (in_array('designer', $userRoles)) {
            $designerPermissions = [
                'sidebar_dashboard', 'sidebar_tasks', 'sidebar_projects_list', 'sidebar_design',
                'sidebar_clients', 'sidebar_training', 'sidebar_meetings',
            ];
            return in_array($permissionKey, $designerPermissions);
        }
        
        // افتراضياً - لا توجد صلاحيات
        return false;
    }

    /**
     * Set custom permission for user
     */
    public function setCustomPermission($permissionKey, $isEnabled = true, $customSettings = null)
    {
        return UserPermission::setPermission($this->id, $permissionKey, $isEnabled, $customSettings);
    }

    public function trainingParticipants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class, 'user_id');
    }

    public function managedSalesTeams(): HasMany
    {
        return $this->hasMany(SalesTeam::class, 'manager_id');
    }

    public function salesTeams(): BelongsToMany
    {
        return $this->belongsToMany(SalesTeam::class, 'sales_team_members', 'user_id', 'sales_team_id')
            ->withTimestamps();
    }

    public function assignedSales(): HasMany
    {
        return $this->hasMany(Sale::class, 'assigned_to');
    }

    public function compensationProfile(): HasOne
    {
        return $this->hasOne(\App\Models\Compensation\CompEmployeeProfile::class);
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(\App\Models\Compensation\CompPayrollRun::class);
    }

    public function assignedCrmTasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'assigned_to');
    }

    public function createdCrmTasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'assigned_by');
    }

    public function isCrmOnlyUser(): bool
    {
        return $this->usesCrmWorkspace();
    }

    /** مستخدم مبيعات (مدير أو وكيل) بدون صلاحيات إدارة النظام الكاملة */
    public function usesCrmWorkspace(): bool
    {
        if ($this->hasRole(['super_admin', 'admin'])) {
            return false;
        }

        return $this->hasRole(array_merge(
            CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
            CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES
        ));
    }

    public function isSalesAgentOnly(): bool
    {
        return $this->hasRole(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            && !$this->hasRole(array_merge(
                ['super_admin', 'admin'],
                CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
                CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            ));
    }

    public function isSalesDepartmentManager(): bool
    {
        return $this->hasRole(CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES);
    }

    public function isSalesTeamLeader(): bool
    {
        return $this->hasRole(CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES)
            || ($this->managedSalesTeams()->exists() && !$this->isSalesDepartmentManager());
    }

    public function isSalesManager(): bool
    {
        return $this->isSalesDepartmentManager() || $this->isSalesTeamLeader();
    }

    public function canAccessCrm(): bool
    {
        return $this->hasRole(array_merge(
            ['super_admin', 'admin'],
            CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES,
            CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES
        ));
    }

    public function usesMarketingWorkspace(): bool
    {
        if ($this->hasRole(['super_admin', 'admin'])) {
            return false;
        }

        return $this->hasRole(array_merge(
            MarketingEmployeeService::LEGACY_MANAGER_ROLES,
            MarketingEmployeeService::LEGACY_REP_ROLES
        ));
    }

    public function isMarketingOnlyUser(): bool
    {
        return $this->usesMarketingWorkspace() && !$this->usesCrmWorkspace();
    }

    public function isMarketingManager(): bool
    {
        return $this->hasRole(MarketingEmployeeService::LEGACY_MANAGER_ROLES);
    }

    public function canAccessMarketing(): bool
    {
        return $this->hasRole(array_merge(
            ['super_admin', 'admin'],
            MarketingEmployeeService::LEGACY_MANAGER_ROLES,
            MarketingEmployeeService::LEGACY_REP_ROLES
        ));
    }

    public function usesOperationsWorkspace(): bool
    {
        if ($this->hasRole(['super_admin', 'admin'])) {
            return false;
        }

        return $this->hasRole(OperationsEmployeeService::LEGACY_MANAGER_ROLES);
    }

    public function isOperationsOnlyUser(): bool
    {
        return $this->usesOperationsWorkspace()
            && !$this->usesCrmWorkspace()
            && !$this->usesMarketingWorkspace();
    }

    public function isOperationsManager(): bool
    {
        return $this->hasRole(OperationsEmployeeService::LEGACY_MANAGER_ROLES);
    }

    public function canAccessOperations(): bool
    {
        return $this->hasRole(array_merge(
            ['super_admin', 'admin'],
            OperationsEmployeeService::LEGACY_MANAGER_ROLES
        ));
    }

    public function homeRoute(): string
    {
        if ($this->isMarketingOnlyUser()) {
            return route('marketing.dashboard');
        }

        if ($this->isOperationsOnlyUser()) {
            return route('operations.dashboard');
        }

        if ($this->usesCrmWorkspace()) {
            return route('crm.dashboard');
        }

        return route('dashboard');
    }
}
