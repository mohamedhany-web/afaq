<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\RealEstateDeveloper;
use App\Models\User;
use App\Services\ProjectUnitGeneratorService;
use Illuminate\Database\Seeder;

class Project5BSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role(['super_admin', 'admin'])->first() ?? User::first();
        if (!$admin) {
            return;
        }

        $developer = RealEstateDeveloper::firstOrCreate(
            ['name' => 'لاكازا'],
            ['created_by' => $admin->id],
        );

        $config = ProjectUnitGeneratorService::defaultConfigFor5B();

        $project = Project::updateOrCreate(
            ['name' => '5B'],
            [
                'description' => implode("\n", [
                    'مشروع 5B — لاكازا',
                    'الاستخدامات: تجاري، إداري، سكني',
                    'الهيكل: بدروم + أرضي + 4 متكرر',
                    'مساحات السكني: 90–170 م² | التجاري: من 40 م²',
                ]),
                'developer_name' => 'لاكازا',
                'real_estate_developer_id' => $developer->id,
                'ownership_type' => 'developer',
                'city' => 'القاهرة',
                'location' => 'المركزية — على محور جمال عبد الناصر مباشرة (في الخلف الاستاد وفندق لامار والبريد)',
                'latitude' => 30.0542,
                'longitude' => 31.3589,
                'land_area_m2' => 31000,
                'property_type' => 'mixed',
                'project_type' => 'tower',
                'listing_status' => 'active',
                'price_from' => 20000,
                'price_to' => 250000,
                'project_manager_id' => $admin->id,
                'start_date' => now()->toDateString(),
                'status' => 'in_progress',
                'priority' => 'high',
                'building_config' => $config,
            ],
        );

        if (!$project->hasGeneratedUnits()) {
            app(ProjectUnitGeneratorService::class)->generate($project, false);
        }
    }
}
