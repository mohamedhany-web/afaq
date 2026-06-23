<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\Operations\OperationsWorkspaceService;
use Illuminate\Support\Facades\View;

$errors = [];
$user = User::query()->first();
$workspace = app(OperationsWorkspaceService::class);

try {
    $sections = $workspace->dashboardSections(null, null);
    if (count($sections) < 10) {
        $errors[] = 'dashboardSections returned too few sections';
    }
    View::make('operations.dashboard', [
        'user' => $user,
        'resolver' => \App\Services\OperationsRoleResolver::for($user),
        'stats' => ['unassigned_leads' => 0, 'pending_absence_reviews' => 0, 'pending_checkout_reviews' => 0, 'active_projects' => 0],
        'kpi' => ['total_score' => 0],
        'period' => app(\App\Services\Compensation\CompensationPayrollService::class)->currentPeriod(),
        'kpiGroups' => [],
        'crmPulse' => null,
        'workspaceSections' => $sections,
        'absenceReviewsLink' => route('operations.attendance-reviews.index'),
        'salesReps' => collect(),
        'selectedSalesRep' => null,
        'clientFilterQuery' => ['view' => 'data'],
    ])->render();
    echo "OK operations.dashboard renders\n";
} catch (\Throwable $e) {
    $errors[] = $e->getMessage();
    echo "FAIL operations.dashboard — {$e->getMessage()}\n";
}

exit($errors === [] ? 0 : 1);
