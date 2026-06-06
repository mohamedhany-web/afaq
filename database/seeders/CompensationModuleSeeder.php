<?php

namespace Database\Seeders;

use App\Models\Compensation\CompBonusRule;
use App\Models\Compensation\CompCommissionPlan;
use App\Models\Compensation\CompDeductionRule;
use App\Models\Compensation\CompEmployeeProfile;
use App\Models\Compensation\CompKpiItem;
use App\Models\Compensation\CompKpiTemplate;
use App\Models\User;
use App\Services\CrmEmployeeService;
use Illuminate\Database\Seeder;

class CompensationModuleSeeder extends Seeder
{
    public function run(): void
    {
        $repTemplate = CompKpiTemplate::firstOrCreate(
            ['name' => 'KPI مندوب مبيعات — شهري'],
            [
                'description' => 'مؤشرات أداء مندوب المبيعات',
                'target_role' => 'rep',
                'evaluation_period' => 'monthly',
                'is_active' => true,
            ],
        );

        $repItems = [
            ['slug' => 'leads_contacted', 'name' => 'عملاء تم التواصل معهم', 'weight' => 5, 'target' => 80],
            ['slug' => 'follow_ups_completed', 'name' => 'متابعات منجزة', 'weight' => 15, 'target' => 60],
            ['slug' => 'property_visits', 'name' => 'معاينات عقارية', 'weight' => 15, 'target' => 20],
            ['slug' => 'qualified_leads', 'name' => 'عملاء مؤهلون', 'weight' => 10, 'target' => 25],
            ['slug' => 'conversion_rate', 'name' => 'معدل التحويل %', 'weight' => 10, 'target' => 25],
            ['slug' => 'closed_deals', 'name' => 'صفقات مغلقة', 'weight' => 20, 'target' => 8],
            ['slug' => 'revenue_generated', 'name' => 'إيراد محقق', 'weight' => 20, 'target' => 2000000],
            ['slug' => 'crm_compliance', 'name' => 'التزام CRM %', 'weight' => 5, 'target' => 90],
        ];

        $this->seedItems($repTemplate, $repItems);

        $mgrTemplate = CompKpiTemplate::firstOrCreate(
            ['name' => 'KPI مدير مبيعات — شهري'],
            [
                'description' => 'مؤشرات أداء مدير المبيعات',
                'target_role' => 'manager',
                'evaluation_period' => 'monthly',
                'is_active' => true,
            ],
        );

        $mgrItems = [
            ['slug' => 'team_revenue', 'name' => 'إيراد الفريق', 'weight' => 25, 'target' => 10000000],
            ['slug' => 'team_conversion_rate', 'name' => 'تحويل الفريق %', 'weight' => 15, 'target' => 30],
            ['slug' => 'team_target_achievement', 'name' => 'تحقيق هدف الفريق %', 'weight' => 20, 'target' => 100],
            ['slug' => 'lead_distribution', 'name' => 'كفاءة توزيع العملاء', 'weight' => 10, 'target' => 50],
            ['slug' => 'follow_up_compliance', 'name' => 'التزام المتابعات %', 'weight' => 15, 'target' => 85],
            ['slug' => 'team_productivity', 'name' => 'إنتاجية الفريق', 'weight' => 10, 'target' => 100],
            ['slug' => 'team_retention', 'name' => 'استبقاء الفريق %', 'weight' => 5, 'target' => 95],
        ];

        $this->seedItems($mgrTemplate, $mgrItems);

        $pctPlan = CompCommissionPlan::firstOrCreate(
            ['name' => 'عمولة نسبة من الصفقة'],
            [
                'model' => 'percentage',
                'config' => ['rate' => 2.5],
                'is_active' => true,
                'description' => '2.5% من قيمة الصفقات المغلقة',
            ],
        );

        CompCommissionPlan::firstOrCreate(
            ['name' => 'عمولة شرائح إيراد'],
            [
                'model' => 'revenue_tier',
                'config' => [
                    'tiers' => [
                        ['min' => 0, 'max' => 1000000, 'rate' => 1],
                        ['min' => 1000000, 'max' => 3000000, 'rate' => 2],
                        ['min' => 3000000, 'max' => null, 'rate' => 3],
                    ],
                ],
                'is_active' => true,
            ],
        );

        CompCommissionPlan::firstOrCreate(
            ['name' => 'مبلغ ثابت لكل صفقة'],
            [
                'model' => 'fixed_per_deal',
                'config' => ['amount' => 5000],
                'is_active' => true,
            ],
        );

        $bonuses = [
            ['code' => 'target_achieved', 'name' => 'مكافأة تحقيق الهدف', 'amount_type' => 'percent_salary', 'amount' => 10],
            ['code' => 'revenue_milestone', 'name' => 'مكافأة إنجاز إيراد', 'amount_type' => 'percent_revenue', 'amount' => 0.5],
            ['code' => 'best_performer', 'name' => 'أفضل أداء', 'amount_type' => 'fixed', 'amount' => 3000],
            ['code' => 'quarterly', 'name' => 'مكافأة ربع سنوية', 'amount_type' => 'fixed', 'amount' => 5000],
            ['code' => 'manager_recommendation', 'name' => 'توصية المدير', 'amount_type' => 'fixed', 'amount' => 0],
            ['code' => 'referral', 'name' => 'مكافأة إحالة', 'amount_type' => 'fixed', 'amount' => 1500],
        ];

        foreach ($bonuses as $b) {
            CompBonusRule::firstOrCreate(['code' => $b['code']], $b + ['is_active' => true, 'conditions' => null]);
        }

        $deductions = [
            ['code' => 'late_attendance', 'name' => 'تأخر حضور', 'amount' => 100],
            ['code' => 'missed_followups', 'name' => 'متابعات فائتة', 'amount' => 200],
            ['code' => 'crm_incomplete', 'name' => 'تحديث CRM ناقص', 'amount' => 150],
            ['code' => 'policy_violation', 'name' => 'مخالفة سياسة', 'amount' => 500],
            ['code' => 'admin_manual', 'name' => 'خصم إداري', 'amount' => 0],
        ];

        foreach ($deductions as $d) {
            CompDeductionRule::firstOrCreate(
                ['code' => $d['code']],
                $d + ['amount_type' => 'fixed', 'requires_approval' => true, 'is_active' => true],
            );
        }

        $users = User::role(array_merge(
            CrmEmployeeService::LEGACY_MANAGER_ROLES,
            CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
        ))->get();

        foreach ($users as $user) {
            $isManager = $user->hasAnyRole(CrmEmployeeService::LEGACY_MANAGER_ROLES);
            CompEmployeeProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'base_salary' => $user->employee?->salary ?? 8000,
                    'kpi_template_id' => $isManager ? $mgrTemplate->id : $repTemplate->id,
                    'commission_plan_id' => $isManager ? null : $pctPlan->id,
                    'is_active' => true,
                    'effective_from' => now()->startOfMonth()->toDateString(),
                ],
            );
        }
    }

    protected function seedItems(CompKpiTemplate $template, array $items): void
    {
        $order = 0;
        foreach ($items as $row) {
            CompKpiItem::updateOrCreate(
                ['template_id' => $template->id, 'slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'weight' => $row['weight'],
                    'target_value' => $row['target'],
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
