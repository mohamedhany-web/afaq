<?php

namespace Database\Seeders;

use App\Models\AutoPenaltyRule;
use Illuminate\Database\Seeder;

class AutoPenaltyRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'مهمة مبيعات متأخرة',
                'department_code' => 'SAL',
                'source_type' => 'crm_task',
                'amount' => 50,
                'grace_hours' => 2,
            ],
            [
                'name' => 'متابعة عميل فائتة',
                'department_code' => 'SAL',
                'source_type' => 'crm_follow_up',
                'amount' => 30,
                'grace_hours' => 1,
            ],
            [
                'name' => 'تقرير مبيعات يومي غير مرفوع',
                'department_code' => 'SAL',
                'source_type' => 'daily_sales_report',
                'amount' => 40,
                'grace_hours' => 4,
            ],
            [
                'name' => 'نشاط تسويق متأخر',
                'department_code' => 'MKT',
                'source_type' => 'marketing_activity',
                'amount' => 50,
                'grace_hours' => 2,
            ],
            [
                'name' => 'تقرير تسويق يومي غير مرفوع',
                'department_code' => 'MKT',
                'source_type' => 'marketing_report',
                'report_period_type' => 'daily',
                'amount' => 40,
                'grace_hours' => 4,
            ],
            [
                'name' => 'تقرير تسويق أسبوعي غير مرفوع (مدير)',
                'department_code' => 'MKT',
                'source_type' => 'marketing_report',
                'report_period_type' => 'weekly',
                'amount' => 100,
                'applies_to' => 'manager',
                'grace_hours' => 24,
            ],
            [
                'name' => 'تقرير تسويق شهري غير مرفوع (مدير)',
                'department_code' => 'MKT',
                'source_type' => 'marketing_report',
                'report_period_type' => 'monthly',
                'amount' => 200,
                'applies_to' => 'manager',
                'grace_hours' => 48,
            ],
        ];

        foreach ($rules as $rule) {
            AutoPenaltyRule::firstOrCreate(
                [
                    'department_code' => $rule['department_code'],
                    'source_type' => $rule['source_type'],
                    'report_period_type' => $rule['report_period_type'] ?? null,
                    'applies_to' => $rule['applies_to'] ?? 'all',
                ],
                array_merge([
                    'description' => 'قاعدة افتراضية — خصم تلقائي عند التأخر',
                    'amount' => 0,
                    'grace_hours' => 0,
                    'is_active' => true,
                ], $rule),
            );
        }
    }
}
