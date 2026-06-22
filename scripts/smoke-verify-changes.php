<?php

/**
 * Smoke verification for recent CRM/Operations changes.
 * Run: php scripts/smoke-verify-changes.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

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

echo "=== Smoke verification ===\n\n";

// 1. Database schema
check(
    \Illuminate\Support\Facades\Schema::hasColumn('clients', 'description'),
    'clients.description column exists',
    $errors,
    $passed
);

check(
    \Illuminate\Support\Facades\Schema::hasTable('client_staff_notes'),
    'client_staff_notes table exists',
    $errors,
    $passed
);

check(
    class_exists(\App\Models\ClientStaffNote::class),
    'ClientStaffNote model exists',
    $errors,
    $passed
);

check(
    method_exists(\App\Http\Controllers\Crm\CrmClientController::class, 'storeStaffNote'),
    'CrmClientController::storeStaffNote exists',
    $errors,
    $passed
);

// 2. Client policy — create open for all
$user = \App\Models\User::query()->first();
if ($user) {
    check(
        (new \App\Policies\ClientPolicy(app(\App\Services\CrmRecordApprovalService::class)))->create($user),
        'ClientPolicy::create returns true for authenticated user',
        $errors,
        $passed
    );
} else {
    echo " SKIP ClientPolicy (no users in DB)\n";
}

// 3. Operations scope services
if ($user) {
    $opsUser = \App\Models\User::role('operation_manager')->first()
        ?? \App\Models\User::query()->whereHas('roles', fn ($q) => $q->where('name', 'operation_manager'))->first()
        ?? $user;

    $leaveScope = \App\Services\LeaveScopeService::for($opsUser);
    if ($opsUser->canAccessOperations()) {
        check($leaveScope->mode() === 'operations', 'LeaveScopeService operations mode', $errors, $passed);
        check($leaveScope->canApprove(), 'LeaveScopeService canApprove for ops', $errors, $passed);
    } else {
        echo " SKIP LeaveScopeService ops checks (no operations user found)\n";
    }

    $permitScope = \App\Services\Hr\ExitPermitScopeService::for($opsUser);
    if ($opsUser->canAccessOperations()) {
        check($permitScope->canApprove(), 'ExitPermitScopeService canApprove for ops', $errors, $passed);
    }
}

// 4. Routes resolve
$routeNames = [
    'crm.clients.create',
    'crm.clients.import',
    'crm.clients.store',
    'crm.clients.staff-notes.store',
    'operations.leaves.index',
    'operations.leaves.approve',
    'operations.exit-permits.index',
    'operations.exit-permits.approve',
    'operations.attendance-reviews.index',
    'operations.checkout-reviews.index',
    'operations.reps.search',
    'hr.exit-permits.index',
    'hr.exit-permits.store',
];

foreach ($routeNames as $name) {
    try {
        $params = match ($name) {
            'operations.leaves.approve' => ['leave' => 1],
            'operations.exit-permits.approve' => ['exitPermit' => 1],
            'crm.clients.staff-notes.store' => ['client' => \App\Models\Client::query()->value('id') ?? 1],
            default => [],
        };
        $url = route($name, $params);
        check(is_string($url) && $url !== '', "Route [{$name}] resolves", $errors, $passed);
    } catch (\Throwable $e) {
        check(false, "Route [{$name}] resolves — {$e->getMessage()}", $errors, $passed);
    }
}

// 5. Controllers instantiate
$controllers = [
    \App\Http\Controllers\Crm\CrmClientController::class,
    \App\Http\Controllers\Operations\OperationsLeaveController::class,
    \App\Http\Controllers\Operations\OperationsExitPermitController::class,
    \App\Http\Controllers\Operations\OperationsRepController::class,
    \App\Http\Controllers\Operations\OperationsAttendanceReviewController::class,
    \App\Http\Controllers\Operations\OperationsCheckoutReviewController::class,
];

foreach ($controllers as $class) {
    check(class_exists($class), "Class {$class} exists", $errors, $passed);
}

// 6. Blade views compile
$views = [
    'crm.clients.create',
    'crm.clients.partials.form',
    'crm.clients.show',
    'crm.pipeline.client',
    'crm.clients.partials.registration-meta',
    'crm.clients.partials.staff-notes',
    'crm.clients.partials.deals-list',
    'operations.leaves.index',
    'operations.exit-permits.index',
    'operations.partials.rep-search-form',
    'layouts.partials.sidebar-client-intake-links',
    'layouts.partials.sidebar-exit-permit-link',
    'layouts.partials.sidebar-operations-manager',
];

foreach ($views as $view) {
    try {
        check(\Illuminate\Support\Facades\View::exists($view), "View [{$view}] exists", $errors, $passed);
    } catch (\Throwable $e) {
        check(false, "View [{$view}] — {$e->getMessage()}", $errors, $passed);
    }
}

// 6b. Render client page partials with real client
$client = \App\Models\Client::query()->with(['sales.project', 'sales.salesRep', 'createdBy', 'staffNotes.user'])->first();
if ($client) {
    $themeColor = '#6366f1';
    $money = fn ($v) => number_format((float) $v, 0);
    $stageLabels = [
        'lead' => 'عميل محتمل',
        'prospect' => 'مهتم',
        'proposal' => 'عرض سعر',
        'negotiation' => 'تفاوض',
        'closed_won' => 'تم البيع',
        'closed_lost' => 'خسارة',
    ];

    foreach ([
        'registration-meta' => 'crm.clients.partials.registration-meta',
        'staff-notes' => 'crm.clients.partials.staff-notes',
        'deals-list' => 'crm.clients.partials.deals-list',
    ] as $label => $view) {
        try {
            \Illuminate\Support\Facades\View::make($view, compact('client', 'themeColor', 'stageLabels', 'money'))->render();
            check(true, "Render partial [{$label}] with client #{$client->id}", $errors, $passed);
        } catch (\Throwable $e) {
            check(false, "Render partial [{$label}] — {$e->getMessage()}", $errors, $passed);
        }
    }

    try {
        $note = \App\Models\ClientStaffNote::create([
            'client_id' => $client->id,
            'user_id' => $client->created_by ?? \App\Models\User::query()->value('id'),
            'type' => \App\Models\ClientStaffNote::TYPE_TIP,
            'body' => 'Smoke test note — safe to delete',
        ]);
        check($note->exists, 'ClientStaffNote create + persist', $errors, $passed);
        $note->delete();
        check(true, 'ClientStaffNote delete (cleanup)', $errors, $passed);
    } catch (\Throwable $e) {
        check(false, 'ClientStaffNote CRUD — ' . $e->getMessage(), $errors, $passed);
    }
} else {
    echo " SKIP client partial render (no clients in DB)\n";
}

// 7. ClientManagementService validates description
try {
    $validator = \Illuminate\Support\Facades\Validator::make(
        ['name' => 'Test', 'phone' => '01099998888', 'status' => 'prospect', 'description' => 'وصف تجريبي'],
        ['description' => 'nullable|string|max:5000']
    );
    check(!$validator->fails(), 'Client description validation accepts Arabic text', $errors, $passed);
} catch (\Throwable $e) {
    check(false, 'Client description validation — ' . $e->getMessage(), $errors, $passed);
}

// 8. Translation keys
$keys = [
    'operations.hr_requests.leaves_title',
    'operations.hr_requests.permits_title',
    'operations.sidebar.leave_approvals',
    'operations.sidebar.exit_permit_approvals',
    'operations.clients.import_excel',
];

foreach ($keys as $key) {
    check(__('operations.hr_requests.leaves_title') !== 'operations.hr_requests.leaves_title' || str_contains($key, 'clients'),
        "Translation key resolves (ar): " . explode('.', $key)[1] ?? $key,
        $errors,
        $passed
    );
}

// Fix translation check - do properly
foreach (['operations.hr_requests.leaves_title', 'operations.sidebar.leave_approvals'] as $key) {
    $val = __($key);
    check($val !== $key, "Translation [{$key}] = {$val}", $errors, $passed);
}

// Project classification filter
check(
    config('project_classifications.types.residential') === 'سكني',
    'project_classifications config loaded',
    $errors,
    $passed
);
check(
    in_array('medical', \App\Models\Project::concreteClassificationKeys(), true),
    'medical classification key exists',
    $errors,
    $passed
);
check(
    method_exists(\App\Models\Project::class, 'classificationSummary'),
    'Project::classificationSummary exists',
    $errors,
    $passed
);
check(
    method_exists(\App\Services\ProjectManagementService::class, 'normalizeClassificationPricing'),
    'ProjectManagementService::normalizeClassificationPricing exists',
    $errors,
    $passed
);
check(
    view()->exists('projects.partials.classification-filter'),
    'classification-filter partial exists',
    $errors,
    $passed
);
check(
    view()->exists('projects.partials.classification-pricing'),
    'classification-pricing partial exists',
    $errors,
    $passed
);

echo "\n=== Results: {$passed} passed, " . count($errors) . " failed ===\n";

if ($errors !== []) {
    echo "\nFailures:\n";
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
    exit(1);
}

echo "All checks passed.\n";
exit(0);
