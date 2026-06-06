<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::updateOrCreate(
            ['code' => 'SAL'],
            [
                'name' => 'المبيعات',
                'description' => 'قسم المبيعات العقارية',
                'is_active' => true,
            ]
        );

        Department::updateOrCreate(
            ['code' => 'MKT'],
            [
                'name' => 'التسويق',
                'description' => 'قسم التسويق والحملات والمحتوى',
                'is_active' => true,
            ]
        );

        Department::whereNotIn('code', ['SAL', 'MKT'])->update(['is_active' => false]);
    }
}
