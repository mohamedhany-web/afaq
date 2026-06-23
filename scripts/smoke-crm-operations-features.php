<?php

/**
 * Verify CRM filters, list columns, operations compact view, bulk actions.
 * Run: php scripts/smoke-crm-operations-features.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Services\Crm\CrmFilterService;
use App\Services\CrmRecordApprovalService;
use App\Services\Operations\OperationsClientBucketService;
use App\Services\Operations\OperationsWorkspaceService;
use Illuminate\Support\Facades\Route;
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

echo "=== CRM / Operations feature verification ===\n\n";

$user = User::query()->first();
$opsUser = User::role('operation_manager')->first()
    ?? User::query()->whereHas('roles', fn ($q) => $q->where('name', 'operation_manager'))->first();

// --- Filters ---
if ($user) {
    $filters = CrmFilterService::for($user);
    $keys = $filters->clientFilterKeys();
    check(in_array('sales_rep', $keys, true), 'clientFilterKeys includes sales_rep', $errors, $passed);
    check(in_array('created_by', $keys, true), 'clientFilterKeys includes created_by', $errors, $passed);
    check(in_array('mine', $keys, true), 'clientFilterKeys includes mine', $errors, $passed);
    check(method_exists($filters, 'showCreatorFilter'), 'CrmFilterService::showCreatorFilter exists', $errors, $passed);
    check($filters->showCreatorFilter(), 'showCreatorFilter true for first user', $errors, $passed);

    $relations = $filters->clientListRelations();
    check(isset($relations['latestInteraction']), 'clientListRelations loads latestInteraction', $errors, $passed);
    check(isset($relations['followUps']), 'clientListRelations loads followUps (next action)', $errors, $passed);
}

if ($opsUser) {
    check($opsUser->canAccessOperations(), 'operation_manager canAccessOperations', $errors, $passed);
    $policy = new ClientPolicy(app(CrmRecordApprovalService::class));
    check($policy->create($opsUser), 'ClientPolicy::create allows operations user', $errors, $passed);
    check($policy->bulkUpdate($opsUser), 'ClientPolicy::bulkUpdate allows operations user', $errors, $passed);

    $opsFilters = CrmFilterService::for($opsUser);
    check($opsFilters->showSalesRepFilter(), 'showSalesRepFilter for operations user', $errors, $passed);
    check($opsFilters->showCreatorFilter(), 'showCreatorFilter for operations user', $errors, $passed);
}

// --- Client list columns helpers ---
$client = Client::query()
    ->with([
        'staffNotes' => fn ($q) => $q->latest()->limit(1),
        'latestInteraction' => fn ($q) => $q
            ->where('status', CrmFollowUp::STATUS_COMPLETED)
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->latest('completed_at')
            ->limit(1),
        'followUps' => fn ($q) => $q->where('status', CrmFollowUp::STATUS_SCHEDULED)->orderBy('scheduled_at')->limit(1),
    ])
    ->first();

if ($client) {
    check(method_exists($client, 'listLatestComment'), 'Client::listLatestComment exists', $errors, $passed);
    check(method_exists($client, 'listNextAction'), 'Client::listNextAction exists', $errors, $passed);
    $comment = $client->listLatestComment();
    check($comment === null || is_string($comment), 'listLatestComment returns string or null', $errors, $passed);
    $next = $client->listNextAction();
    check($next === null || (is_array($next) && isset($next['label'], $next['at'])), 'listNextAction shape valid', $errors, $passed);

    try {
        View::make('crm.clients.partials.list-comment', compact('client'))->render();
        View::make('crm.clients.partials.list-next-action', compact('client'))->render();
        check(true, 'list-comment + list-next-action partials render', $errors, $passed);
    } catch (\Throwable $e) {
        check(false, 'list partials render — ' . $e->getMessage(), $errors, $passed);
    }
} else {
    echo " SKIP client list column tests (no clients)\n";
}

// --- Operations routes ---
$routeNames = [
    'operations.dashboard',
    'operations.clients.index',
    'operations.clients.create',
    'operations.clients.store',
    'operations.clients.bulk-transfer',
    'operations.clients.bulk-update-meta',
    'operations.clients.bulk-destroy',
    'operations.crm.index',
    'crm.clients.index',
    'crm.clients.staff-notes.store',
    'crm.clients.staff-notes.update',
    'crm.clients.staff-notes.destroy',
];

foreach ($routeNames as $name) {
    try {
        $params = match ($name) {
            'crm.clients.staff-notes.store' => ['client' => $client?->id ?? 1],
            'crm.clients.staff-notes.update' => ['client' => $client?->id ?? 1, 'note' => 1],
            'crm.clients.staff-notes.destroy' => ['client' => $client?->id ?? 1, 'note' => 1],
            default => [],
        };
        $url = route($name, $params);
        check(is_string($url) && $url !== '', "Route [{$name}] resolves", $errors, $passed);
    } catch (\Throwable $e) {
        check(false, "Route [{$name}] — {$e->getMessage()}", $errors, $passed);
    }
}

// --- Operations workspace sections (New before follow_up) ---
$workspace = app(OperationsWorkspaceService::class);
$sections = $workspace->dashboardSections(null, null);
$keys = array_column($sections, 'key');
$newIdx = array_search('new', $keys, true);
$followIdx = array_search('follow_up', $keys, true);
check($newIdx !== false && $followIdx !== false && $newIdx < $followIdx, 'Dashboard: New section before follow_up', $errors, $passed);

$bucketLabels = app(OperationsClientBucketService::class)->labels();
$bucketKeys = array_keys($bucketLabels);
check(array_search('new', $bucketKeys, true) < array_search('follow_up', $bucketKeys, true), 'Buckets: new before follow_up', $errors, $passed);

// --- sales_rep in workspace href when rep selected ---
if ($opsUser) {
    $repSections = $workspace->dashboardSections(null, $opsUser);
    $allSection = collect($repSections)->firstWhere('key', 'all');
    $href = $allSection['href'] ?? '';
    check(str_contains($href, 'sales_rep=' . $opsUser->id), 'Workspace links preserve sales_rep filter', $errors, $passed);
}

// --- Views compile ---
$views = [
    'crm.clients.index',
    'crm.partials.filter-bar',
    'crm.clients.partials.bulk-actions',
    'crm.clients.partials.staff-notes',
    'operations.dashboard',
    'operations.clients.index',
    'operations.clients.partials.data-panel',
    'operations.partials.compact-toolbar',
    'operations.partials.kpi-group',
    'operations.crm.index',
    'operations.team.index',
    'operations.inventory.index',
    'operations.reports.index',
    'operations.leads.index',
];

foreach ($views as $view) {
    check(View::exists($view), "View [{$view}] exists", $errors, $passed);
}

if ($user && $client) {
    $themeColor = '#6366f1';
    $filters = CrmFilterService::for($user);
    $stageLabels = \App\Services\CrmScopeService::leadStageLabels();
    $statusLabels = ['prospect' => 'محتمل', 'active' => 'نشط'];
    $viewData = [
        'clients' => Client::query()->with($filters->clientListRelations())->paginate(5),
        'stats' => ['total' => 1, 'prospect' => 1, 'active' => 0, 'with_deals' => 0],
        'assignableReps' => collect(),
        'selectedSalesRep' => null,
        'requiresApproval' => false,
        'requiresMutationApproval' => false,
        'clearUrl' => route('crm.clients.index'),
        'themeColor' => $themeColor,
        'mode' => 'clients',
        'filterKeys' => $filters->clientFilterKeys(),
        'advancedKeys' => [],
        'hasActive' => false,
        'salesReps' => $filters->salesReps(),
        'creatorUsers' => $filters->creatorUsers(),
        'showSalesRepFilter' => $filters->showSalesRepFilter(),
        'showCreatorFilter' => $filters->showCreatorFilter(),
        'stageLabels' => $stageLabels,
        'statusOptions' => $statusLabels,
        'leadSources' => Client::leadSourceLabels(),
        'searchPlaceholder' => 'بحث',
        'action' => route('crm.clients.index'),
        'clientsRoutePrefix' => 'crm.clients',
    ];

    try {
        View::make('crm.clients.index', $viewData)->render();
        check(true, 'crm.clients.index renders with filters', $errors, $passed);
    } catch (\Throwable $e) {
        check(false, 'crm.clients.index render — ' . $e->getMessage(), $errors, $passed);
    }

    try {
        View::make('crm.clients.partials.bulk-actions', [
            'assignableReps' => collect(),
            'clientsRoutePrefix' => 'operations.clients',
        ])->render();
        $html = View::make('crm.clients.partials.bulk-actions', [
            'assignableReps' => collect(),
            'clientsRoutePrefix' => 'operations.clients',
        ])->render();
        check(str_contains($html, 'operations.clients.bulk-destroy') || str_contains($html, 'bulk-delete'), 'bulk-actions uses operations route prefix', $errors, $passed);
    } catch (\Throwable $e) {
        check(false, 'bulk-actions render — ' . $e->getMessage(), $errors, $passed);
    }
}

// --- Compact mode CSS hook ---
$layout = file_get_contents(base_path('resources/views/layouts/app.blade.php'));
check(str_contains($layout, 'ui-compact-mode'), 'Layout defines ui-compact-mode CSS', $errors, $passed);
check(str_contains($layout, 'ui-compact-scripts'), 'Layout includes ui-compact-scripts', $errors, $passed);

$compactToolbar = file_get_contents(base_path('resources/views/operations/partials/compact-toolbar.blade.php'));
check(str_contains($compactToolbar, 'ui-compact-toggle'), 'compact-toolbar includes toggle', $errors, $passed);

echo "\n=== Results: {$passed} passed, " . count($errors) . " failed ===\n";

if ($errors !== []) {
    echo "\nFailures:\n";
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
    exit(1);
}

echo "All CRM/Operations feature checks passed.\n";
exit(0);
