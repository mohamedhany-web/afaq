<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\UserPermission;
use App\Models\Project;
use App\Models\DailySalesReport;
use App\Models\DepartmentReport;
use App\Models\MarketingPeriodReport;
use App\Policies\DailySalesReportPolicy;
use App\Policies\MarketingPeriodReportPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\DepartmentReportPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        DepartmentReport::class => DepartmentReportPolicy::class,
        DailySalesReport::class => DailySalesReportPolicy::class,
        MarketingPeriodReport::class => MarketingPeriodReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // التحقق من الصلاحيات المخصصة على مستوى المستخدم قبل صلاحيات الأدوار
        Gate::before(function ($user, string $ability, array $arguments = []) {
            if ($arguments !== [] && ! is_string($arguments[0] ?? null)) {
                return null;
            }

            // التحقق من وجود صلاحية مخصصة للمستخدم في جدول user_permissions
            $customPermission = UserPermission::where('user_id', $user->id)
                ->where('permission_key', $ability)
                ->first();
            
            // إذا كان هناك صلاحية مخصصة، نستخدمها (سواء كانت مفعلة أو معطلة)
            if ($customPermission) {
                return $customPermission->is_enabled ? true : false;
            }
            
            // إذا لم توجد صلاحية مخصصة، نترك Spatie يتعامل مع صلاحيات الأدوار
            return null; // null يعني متابعة الفحص العادي
        });
    }
}