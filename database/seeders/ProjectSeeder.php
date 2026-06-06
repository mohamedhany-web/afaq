<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (Project::exists()) {
            $this->command?->info('ProjectSeeder: المشاريع موجودة مسبقاً — تم التخطي');

            return;
        }

        $managerId = User::query()->value('id');

        $projects = [
            [
                'name' => 'كمبوند سيتي فيو',
                'description' => 'كمبوند سكني متكامل بخدمات ومرافق — شقق ودوبلكس بإطلالات مفتوحة.',
                'developer_name' => 'City View Developments',
                'city' => 'القاهرة الجديدة',
                'location' => 'التجمع الخامس',
                'property_type' => 'residential',
                'project_type' => 'compound',
                'listing_status' => 'active',
                'total_units' => 320,
                'sold_units' => 145,
                'available_units' => 175,
                'price_from' => 2500000,
                'price_to' => 8500000,
                'project_manager_id' => $managerId,
                'status' => 'in_progress',
                'priority' => 'medium',
                'start_date' => now()->subMonths(6),
            ],
            [
                'name' => 'أبراج سيتي بلازا',
                'description' => 'برج تجاري سكني — محلات ووحدات إدارية وشقق فندقية.',
                'developer_name' => 'City View Developments',
                'city' => '6 أكتوبر',
                'location' => 'الحي المتميز',
                'property_type' => 'mixed',
                'project_type' => 'tower',
                'listing_status' => 'upcoming',
                'total_units' => 180,
                'sold_units' => 20,
                'available_units' => 160,
                'price_from' => 1800000,
                'price_to' => 12000000,
                'project_manager_id' => $managerId,
                'status' => 'planning',
                'priority' => 'high',
                'start_date' => now()->addMonth(),
            ],
            [
                'name' => 'فillas View',
                'description' => 'مجمع فلل مستقلة بحدائق خاصة ونادي اجتماعي.',
                'developer_name' => 'City View Developments',
                'city' => 'العاصمة الإدارية',
                'location' => 'R7',
                'property_type' => 'villa',
                'project_type' => 'villas',
                'listing_status' => 'active',
                'total_units' => 85,
                'sold_units' => 62,
                'available_units' => 23,
                'price_from' => 8500000,
                'price_to' => 18000000,
                'project_manager_id' => $managerId,
                'status' => 'in_progress',
                'priority' => 'medium',
                'start_date' => now()->subYear(),
            ],
        ];

        foreach ($projects as $data) {
            Project::create($data);
        }

        $this->command?->info('ProjectSeeder: تم إضافة ' . count($projects) . ' مشاريع عقارية تجريبية');
    }
}
