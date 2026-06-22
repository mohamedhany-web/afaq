<?php

/**
 * اختبار شامل للمشاريع العقارية: حفظ، تحديث، خريطة، عرض الصفحات
 * Run: php scripts/test-projects-full.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Crm\CrmProjectController;
use App\Http\Controllers\Operations\OperationsProjectController;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectApprovalService;
use App\Services\ProjectManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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

echo "=== اختبار شامل للمشاريع العقارية ===\n\n";

$user = User::role('super_admin')->first()
    ?? User::role('admin')->first()
    ?? User::role('sales_manager')->first()
    ?? User::role('operation_manager')->first();
if (! $user) {
    $user = User::query()->whereHas('permissions', fn ($q) => $q->where('name', 'create-projects'))->first();
}
if (! $user) {
    $user = User::query()->first();
}
if (! $user) {
    echo "لا يوجد مستخدم في قاعدة البيانات.\n";
    exit(1);
}

Auth::login($user);
$approval = app(ProjectApprovalService::class);
$ctrl = app(CrmProjectController::class);
$service = app(ProjectManagementService::class);

// تأكد من صلاحيات الاختبار (بيئة محلية قد لا تحتوي مستخدمين بأدوار)
$grantedTestRole = false;
if (! $user->hasRole('super_admin') && ! $user->hasRole('admin')) {
    $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        $user->assignRole($superAdminRole);
        $grantedTestRole = true;
        $user = $user->fresh();
        Auth::login($user);
    }
}

$needsApproval = $approval->requiresApproval($user);

echo "المستخدم: {$user->email}" . ($needsApproval ? ' (يتطلب موافقة)' : '') . "\n\n";

echo "--- اختبار 5: HTTP store عبر المتحكم ---\n";
DB::beginTransaction();
try {
    $payload = [
        'name' => 'HTTP Store Test ' . date('His'),
        'inventory_source' => 'company',
        'ownership_type' => 'afaq_private',
        'property_types' => ['residential'],
        'listing_status' => 'active',
        'city' => 'القاهرة',
        'ownership_details' => ['internal_entity' => 'أفاق'],
        'manual_units' => [
            ['use_type' => 'residential', 'area_m2' => 100, 'apartment_number' => '101', 'unit_price_total' => 2000000, 'down_percent' => 10, 'years' => 5],
            ['use_type' => 'residential', 'area_m2' => 110, 'apartment_number' => '102', 'unit_price_total' => 2200000, 'down_percent' => 15, 'years' => 6],
        ],
        'map_pins' => [
            ['title' => 'الموقع الرئيسي', 'pin_type' => 'project', 'latitude' => 30.10, 'longitude' => 31.30],
        ],
    ];
    $req = Request::create('/crm/projects', 'POST', $payload);
    $req->setUserResolver(fn () => $user);

    if ($needsApproval) {
        echo "  SKIP HTTP store (يتطلب موافقة إدارية)\n";
    } else {
        $resp = $ctrl->store($req);
        check($resp->getStatusCode() === 302, 'store يعيد redirect', $errors, $passed);
        $project = Project::where('name', $payload['name'])->first();
        check($project !== null, 'المشروع محفوظ في DB', $errors, $passed);
        if ($project) {
            check($project->units()->count() === 2, 'وحدتان يدويتان محفوظتان', $errors, $passed);
            check($project->mapPins()->count() === 1, 'علامة خريطة واحدة', $errors, $passed);
            check(abs((float) $project->latitude - 30.10) < 0.001, 'خط العرض من الخريطة (غير افتراضي)', $errors, $passed);
            check($project->total_units >= 2, 'total_units محدّث', $errors, $passed);
            check($project->available_units >= 2, 'available_units محدّث', $errors, $passed);
        }
    }
    DB::rollBack();
} catch (\Throwable $e) {
    DB::rollBack();
    $msg = $e->getMessage() ?: get_class($e) . ' (code ' . $e->getCode() . ')';
    $errors[] = 'HTTP store: ' . $msg;
    echo " FAIL HTTP store: {$msg}\n";
}

echo "\n--- اختبار 6: تحديث المشروع ---\n";
DB::beginTransaction();
try {
    $payload = [
        'name' => 'Update Test ' . date('His'),
        'inventory_source' => 'company',
        'ownership_type' => 'afaq_private',
        'property_types' => ['residential'],
        'listing_status' => 'active',
        'manual_units' => [
            ['use_type' => 'residential', 'area_m2' => 90, 'apartment_number' => 'A1', 'unit_price_total' => 1500000],
        ],
    ];
    $req = Request::create('/crm/projects', 'POST', $payload);
    $data = $service->normalize($service->validate($req), $req, $user);
    $data = $service->resolveDeveloper($data, $user);
    $project = Project::create($data);
    $service->syncManualUnits($project, $payload['manual_units']);

    $updatePayload = array_merge($payload, [
        'name' => 'Updated Name ' . date('His'),
        'city' => 'الإسكندرية',
        'listing_status' => 'upcoming',
        'manual_units' => [
            ['use_type' => 'residential', 'area_m2' => 95, 'apartment_number' => 'B1', 'unit_price_total' => 1600000],
            ['use_type' => 'residential', 'area_m2' => 100, 'apartment_number' => 'B2', 'unit_price_total' => 1700000],
        ],
    ]);
    $updReq = Request::create('/crm/projects/' . $project->id, 'PUT', $updatePayload);
    $updReq->setUserResolver(fn () => $user);

    if ($needsApproval) {
        echo "  SKIP update (يتطلب موافقة إدارية)\n";
    } else {
        $resp = $ctrl->update($updReq, $project->fresh());
        check($resp->getStatusCode() === 302, 'update يعيد redirect', $errors, $passed);
        $project->refresh();
        check(str_contains($project->name, 'Updated Name'), 'الاسم محدّث', $errors, $passed);
        check($project->city === 'الإسكندرية', 'المدينة محدّثة', $errors, $passed);
        check($project->listing_status === 'upcoming', 'حالة العرض محدّثة', $errors, $passed);
        check($project->units()->count() === 2, 'الوحدات استُبدلت (2)', $errors, $passed);
    }
    DB::rollBack();
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'update: ' . $e->getMessage();
    echo " FAIL update: {$e->getMessage()}\n";
}

echo "\n--- اختبار 7: عرض الصفحات (Blade) ---\n";
DB::beginTransaction();
try {
    $project = Project::query()->first();
    if (! $project) {
        $payload = [
            'name' => 'View Test ' . date('His'),
            'inventory_source' => 'company',
            'ownership_type' => 'afaq_private',
            'property_types' => ['residential'],
            'listing_status' => 'active',
        ];
        $req = Request::create('/', 'POST', $payload);
        $data = $service->normalize($service->validate($req), $req, $user);
        $project = Project::create($service->resolveDeveloper($data, $user));
    }

    $viewTests = [
        'index' => function () use ($user, $ctrl) {
            $req = Request::create('/crm/projects', 'GET');
            $req->setUserResolver(fn () => $user);

            return (array) $ctrl->index($req)->getData();
        },
        'create' => fn () => (array) $ctrl->create()->getData(),
        'show' => fn () => (array) $ctrl->show($project)->getData(),
        'edit' => fn () => (array) $ctrl->edit($project)->getData(),
    ];

    foreach ($viewTests as $action => $dataFn) {
        $viewName = 'crm.projects.' . $action;
        try {
            $data = $dataFn();
            $html = View::make($viewName, $data)->render();
            check(strlen($html) > 100, "عرض {$viewName} يُجمّع", $errors, $passed);
            check(! str_contains($html, 'Undefined variable'), "لا أخطاء متغيرات في {$viewName}", $errors, $passed);
        } catch (\Throwable $e) {
            $errors[] = "view {$viewName}: " . $e->getMessage();
            echo " FAIL view {$viewName}: {$e->getMessage()}\n";
        }
    }
    DB::rollBack();
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'views: ' . $e->getMessage();
    echo " FAIL views: {$e->getMessage()}\n";
}

echo "\n--- اختبار 8: التحقق من صحة البيانات (validation) ---\n";
try {
    $badReq = Request::create('/crm/projects', 'POST', [
        'name' => '',
        'inventory_source' => 'invalid',
        'ownership_type' => 'invalid',
        'property_types' => [],
        'listing_status' => 'invalid',
    ]);
    $failed = false;
    try {
        $service->validate($badReq);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $failed = true;
        check(isset($e->errors()['name']), 'رفض اسم فارغ', $errors, $passed);
        check(count($e->errors()) >= 3, 'رفض حقول متعددة خاطئة', $errors, $passed);
    }
    check($failed, 'التحقق يرفض بيانات غير صالحة', $errors, $passed);
} catch (\Throwable $e) {
    $errors[] = 'validation: ' . $e->getMessage();
    echo " FAIL validation: {$e->getMessage()}\n";
}

echo "\n--- اختبار 9: submitCreate (موافقة) بدون خطأ \$project ---\n";
DB::beginTransaction();
try {
    $payload = [
        'name' => 'Approval Create ' . date('His'),
        'inventory_source' => 'company',
        'ownership_type' => 'afaq_private',
        'property_types' => ['residential'],
        'listing_status' => 'active',
    ];
    $req = Request::create('/crm/projects', 'POST', $payload);
    $change = $approval->submitCreate($req, $user);
    check($change->exists, 'طلب الموافقة يُنشأ', $errors, $passed);
    check(($change->payload['project']['name'] ?? '') === $payload['name'], 'اسم المشروع في الطلب صحيح', $errors, $passed);
    DB::rollBack();
} catch (\Throwable $e) {
    DB::rollBack();
    $errors[] = 'approval create: ' . $e->getMessage();
    echo " FAIL approval create: {$e->getMessage()}\n";
}

Auth::logout();

if ($grantedTestRole ?? false) {
    $user->removeRole('super_admin');
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
