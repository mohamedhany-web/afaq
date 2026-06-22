<?php

/**
 * اختبار إضافة مشروع عقاري + وحدات يدوية
 * Run: php scripts/test-project-inventory.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use App\Models\RealEstateDeveloper;
use App\Models\User;
use App\Services\ProjectManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$errors = [];
$passed = 0;

function check(bool $ok, string $label, array &$errors, int &$passed): void
{
    if ($ok) {
        $passed++;
        echo "  OK  {$label}\n";
    } else {
        $errors[] = $label;
        echo " FAIL {$label}\n";
    }
}

echo "=== اختبار المشاريع العقارية ===\n\n";

// Schema
check(Schema::hasTable('projects'), 'جدول projects موجود', $errors, $passed);
check(Schema::hasColumn('projects', 'inventory_source'), 'عمود inventory_source موجود', $errors, $passed);
check(Schema::hasTable('project_units'), 'جدول project_units موجود', $errors, $passed);
check(Schema::hasTable('building_floors'), 'جدول building_floors موجود', $errors, $passed);
check(Schema::hasTable('unit_payment_plans'), 'جدول unit_payment_plans موجود', $errors, $passed);

if (Schema::hasTable('project_units')) {
    check(Schema::hasColumn('project_units', 'direction'), 'عمود direction في الوحدات', $errors, $passed);
    check(Schema::hasColumn('project_units', 'apartment_number'), 'عمود apartment_number', $errors, $passed);
}

if (Schema::hasTable('unit_payment_plans')) {
    check(Schema::hasColumn('unit_payment_plans', 'total_contract_amount'), 'عمود total_contract_amount', $errors, $passed);
}

$user = User::query()->first();
if (! $user) {
    echo "\n⚠ لا يوجد مستخدم — إنشاء مستخدم اختبار...\n";
    $user = User::factory()->create(['name' => 'Test Admin', 'email' => 'test-project-' . time() . '@test.local']);
}

try {
    if (method_exists($user, 'can') && ! $user->can('view-all-projects')) {
        $user->givePermissionTo('view-all-projects');
    }
} catch (\Throwable) {
    $admin = User::role(['super_admin', 'admin', 'sales_manager'])->first();
    if ($admin) {
        $user = $admin;
    }
}

$service = app(ProjectManagementService::class);

echo "\n--- اختبار 1: مشروع وحدات الشركة + وحدات يدوية ---\n";

DB::beginTransaction();
try {
    $payload = [
        'name' => 'اختبار وحدات الشركة ' . date('His'),
        'inventory_source' => 'company',
        'ownership_type' => 'afaq_private',
        'property_types' => ['residential'],
        'listing_status' => 'active',
        'city' => 'القاهرة',
        'location' => 'التجمع',
        'total_units' => 0,
        'sold_units' => 0,
        'ownership_details' => ['internal_entity' => 'أفاق'],
        'manual_units' => [
            [
                'use_type' => 'residential',
                'area_m2' => 120,
                'direction' => 'north',
                'floor_number' => '3',
                'floor_label' => 'ثالث',
                'apartment_number' => '301',
                'unit_price_total' => 2500000,
                'building_percent' => 5,
                'discount_percent' => 2,
                'loading_percent' => 10,
                'maintenance_deposit' => 50000,
                'down_percent' => 20,
                'years' => 7,
            ],
        ],
    ];

    $request = Request::create('/crm/projects', 'POST', $payload);
    $validated = $service->validate($request);
    check(is_array($validated), 'التحقق من بيانات مشروع الشركة نجح', $errors, $passed);

    $data = $service->normalize($validated, $request, $user);
    check($data['inventory_source'] === 'company', 'inventory_source = company', $errors, $passed);
    check($data['ownership_type'] === 'afaq_private', 'ownership_type = afaq_private', $errors, $passed);

    $data = $service->resolveDeveloper($data, $user);
    $project = Project::create($data);
    check($project->exists, 'إنشاء المشروع في DB', $errors, $passed);

    $savedUnits = $service->syncManualUnits($project, $payload['manual_units']);
    check($savedUnits === 1, "حفظ {$savedUnits} وحدة يدوية", $errors, $passed);

    $project->refresh();
    $unit = $project->units()->with('paymentPlans')->first();
    check($unit !== null, 'الوحدة موجودة بعد الحفظ', $errors, $passed);
    if ($unit) {
        check((float) $unit->area_m2 === 120.0, 'مساحة الوحدة = 120', $errors, $passed);
        check($unit->apartment_number === '301', 'رقم الشقة = 301', $errors, $passed);
        check($unit->direction === 'north', 'الاتجاه = north', $errors, $passed);
        $plan = $unit->paymentPlans->first();
        check($plan !== null, 'خطة السداد موجودة', $errors, $passed);
        if ($plan) {
            check($plan->total_contract_amount > 0, 'إجمالي العقد محسوب (>0)', $errors, $passed);
            check($plan->down_payment_amount > 0, 'المقدم محسوب (>0)', $errors, $passed);
            echo "       → إجمالي العقد: {$plan->total_contract_amount} | المقدم: {$plan->down_payment_amount}\n";
        }
    }

    DB::rollBack();
    echo "  (تم التراجع — لا تغيير دائم)\n";
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'مشروع الشركة: ' . $e->getMessage();
    echo " FAIL مشروع الشركة: {$e->getMessage()}\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n--- اختبار 2: مشروع المطور + قائمة منسدلة ---\n";

DB::beginTransaction();
try {
    $developer = RealEstateDeveloper::contracted()->first();
    if (! $developer) {
        $developer = RealEstateDeveloper::query()->create([
            'name' => 'مطور اختبار',
            'status' => RealEstateDeveloper::STATUS_ACTIVE,
            'created_by' => $user->id,
        ]);
        \App\Models\DeveloperContract::query()->create([
            'real_estate_developer_id' => $developer->id,
            'status' => \App\Models\DeveloperContract::STATUS_ACTIVE,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addYear(),
            'created_by' => $user->id,
        ]);
        $developer = RealEstateDeveloper::contracted()->first();
    }
    if (! $developer) {
        echo " SKIP مشروع المطور (تعذّر إنشاء مطور اختبار)\n";
    } else {
        $payload = [
            'name' => 'اختبار مطور ' . date('His'),
            'inventory_source' => 'developer',
            'ownership_type' => 'developer',
            'real_estate_developer_id' => $developer->id,
            'developer_name' => $developer->name,
            'property_types' => ['residential', 'commercial'],
            'listing_status' => 'active',
            'city' => 'العاصمة الإدارية',
            'classification_pricing' => [
                'residential' => [
                    'price_from' => 2000000,
                    'price_to' => 4000000,
                    'area_from' => 100,
                    'area_to' => 200,
                    'building_percent' => 3,
                    'discount_percent' => 1,
                    'loading_percent' => 8,
                    'maintenance_deposit' => 30000,
                    'default_down_percent' => 15,
                    'default_installment_years' => 8,
                ],
                'commercial' => [
                    'price_from' => 5000000,
                    'price_to' => 9000000,
                    'area_from' => 50,
                    'area_to' => 120,
                ],
            ],
        ];

        $request = Request::create('/crm/projects', 'POST', $payload);
        $validated = $service->validate($request);
        $data = $service->normalize($validated, $request, $user);
        $data = $service->resolveDeveloper($data, $user);

        check($data['inventory_source'] === 'developer', 'inventory_source = developer', $errors, $passed);
        check((int) $data['real_estate_developer_id'] === (int) $developer->id, 'ربط المطور صحيح', $errors, $passed);

        $pricing = $data['building_config']['classification_pricing'] ?? [];
        check(isset($pricing['residential']['building_percent']), 'حفظ نسبة البناء في التصنيف', $errors, $passed);

        $project = Project::create($data);
        check($project->price_from > 0, 'price_from مُزامَن من التصنيفات', $errors, $passed);

        DB::rollBack();
        echo "  (تم التراجع — لا تغيير دائم)\n";
    }
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'مشروع المطور: ' . $e->getMessage();
    echo " FAIL مشروع المطور: {$e->getMessage()}\n";
}

echo "\n--- اختبار 3: مشروع وحدات الغير ---\n";

DB::beginTransaction();
try {
    $payload = [
        'name' => 'اختبار وحدات الغير ' . date('His'),
        'inventory_source' => 'non_company',
        'ownership_type' => 'direct_owner',
        'property_types' => ['commercial'],
        'listing_status' => 'active',
        'ownership_details' => ['contact_name' => 'مالك تجريبي', 'contact_phone' => '01000000000'],
        'manual_units' => [
            [
                'use_type' => 'commercial',
                'area_m2' => 85,
                'direction' => 'east',
                'floor_number' => '1',
                'floor_label' => 'أرضي',
                'apartment_number' => 'SH-01',
                'unit_price_total' => 1800000,
                'down_percent' => 25,
                'years' => 5,
            ],
        ],
    ];

    $request = Request::create('/crm/projects', 'POST', $payload);
    $validated = $service->validate($request);
    $data = $service->normalize($validated, $request, $user);
    $data = $service->resolveDeveloper($data, $user);
    $project = Project::create($data);
    $saved = $service->syncManualUnits($project, $payload['manual_units']);

    check($data['inventory_source'] === 'non_company', 'inventory_source = non_company', $errors, $passed);
    check($saved === 1, 'حفظ وحدة وحدات الغير', $errors, $passed);

    DB::rollBack();
    echo "  (تم التراجع — لا تغيير دائم)\n";
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'وحدات الغير: ' . $e->getMessage();
    echo " FAIL وحدات الغير: {$e->getMessage()}\n";
}

echo "\n--- اختبار 4: تصدير CSV ---\n";
try {
    \Illuminate\Support\Facades\Auth::login($user);
    $exportRequest = Request::create('/crm/projects/export', 'GET');
    $exportRequest->setUserResolver(fn () => $user);
    $response = app(\App\Http\Controllers\Crm\CrmProjectController::class)->export($exportRequest);
    check($response->getStatusCode() === 200, 'تصدير CSV يعيد 200', $errors, $passed);
    \Illuminate\Support\Facades\Auth::logout();
} catch (\Throwable $e) {
    \Illuminate\Support\Facades\Auth::logout();
    $errors[] = 'تصدير: ' . $e->getMessage();
    echo " FAIL تصدير: {$e->getMessage()}\n";
}

echo "\n=== النتيجة: {$passed} نجح";
if ($errors !== []) {
    echo ' | ' . count($errors) . " فشل:\n";
    foreach ($errors as $err) {
        echo "  - {$err}\n";
    }
    exit(1);
}

echo " — لا أخطاء\n";
exit(0);
