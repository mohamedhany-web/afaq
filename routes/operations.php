<?php

use App\Http\Controllers\Operations\OperationsAttendanceReviewController;
use App\Http\Controllers\Operations\OperationsCheckoutReviewController;
use App\Http\Controllers\Operations\OperationsClientController;
use App\Http\Controllers\Operations\OperationsCrmController;
use App\Http\Controllers\Operations\OperationsDashboardController;
use App\Http\Controllers\Operations\OperationsFollowUpController;
use App\Http\Controllers\Operations\OperationsInventoryController;
use App\Http\Controllers\Operations\OperationsExitPermitController;
use App\Http\Controllers\Operations\OperationsLeadController;
use App\Http\Controllers\Operations\OperationsLeaveController;
use App\Http\Controllers\Operations\OperationsProjectController;
use App\Http\Controllers\Operations\OperationsProjectUnitController;
use App\Http\Controllers\Operations\OperationsRepController;
use App\Http\Controllers\Operations\OperationsPeriodReportController;
use App\Http\Controllers\Operations\OperationsTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('operations')->name('operations.')->group(function () {
    Route::get('/', [OperationsDashboardController::class, 'index'])->name('dashboard');

    Route::get('leads', [OperationsLeadController::class, 'index'])->name('leads.index');
    Route::get('clients/check-phone', [OperationsClientController::class, 'checkPhone'])->name('clients.check-phone');
    Route::get('clients/export', [OperationsClientController::class, 'export'])->name('clients.export');
    Route::post('clients/bulk-transfer', [OperationsClientController::class, 'bulkTransfer'])->name('clients.bulk-transfer');
    Route::post('clients/bulk-update-meta', [OperationsClientController::class, 'bulkUpdateMeta'])->name('clients.bulk-update-meta');
    Route::post('clients/bulk-delete', [OperationsClientController::class, 'bulkDestroy'])->name('clients.bulk-destroy');
    Route::post('clients/{client}/transfer', [OperationsClientController::class, 'transfer'])->name('clients.transfer');
    Route::get('clients/create', [OperationsClientController::class, 'create'])->name('clients.create');
    Route::get('clients/import/template', [OperationsClientController::class, 'importTemplate'])->name('clients.import.template');
    Route::post('clients/import', [OperationsClientController::class, 'import'])->name('clients.import');
    Route::post('clients', [OperationsClientController::class, 'store'])->name('clients.store');
    Route::get('clients', [OperationsClientController::class, 'index'])->name('clients.index');
    Route::get('reps/search', [OperationsRepController::class, 'search'])->name('reps.search');
    Route::get('reps/{rep}', [OperationsRepController::class, 'show'])->name('reps.show');
    Route::post('leads/{client}/assign', [OperationsLeadController::class, 'assign'])->name('leads.assign');
    Route::post('leads/distribute-batch', [OperationsLeadController::class, 'distributeBatch'])->name('leads.distribute-batch');
    Route::post('leads/auto-distribute', [OperationsLeadController::class, 'autoDistribute'])->name('leads.auto-distribute');

    Route::get('crm', [OperationsCrmController::class, 'index'])->name('crm.index');

    Route::get('follow-ups', [OperationsFollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('follow-ups', [OperationsFollowUpController::class, 'store'])->name('follow-ups.store');
    Route::patch('follow-ups/{followUp}/complete', [OperationsFollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::patch('follow-ups/{followUp}/cancel', [OperationsFollowUpController::class, 'cancel'])->name('follow-ups.cancel');

    Route::get('inventory/export', [OperationsInventoryController::class, 'export'])->name('inventory.export');
    Route::get('inventory', [OperationsInventoryController::class, 'index'])->name('inventory.index');

    Route::get('projects/export', [OperationsProjectController::class, 'export'])->name('projects.export');
    Route::get('projects', [OperationsProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [OperationsProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [OperationsProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [OperationsProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [OperationsProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [OperationsProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [OperationsProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('projects/{project}/units/generate', [OperationsProjectUnitController::class, 'generate'])->name('projects.units.generate');
    Route::post('projects/{project}/units/renumber', [OperationsProjectUnitController::class, 'renumber'])->name('projects.units.renumber');
    Route::patch('projects/{project}/units/{unit}', [OperationsProjectUnitController::class, 'update'])->name('projects.units.update');
    Route::get('projects/{project}/units/{unit}', [OperationsProjectUnitController::class, 'show'])->name('projects.units.show');
    Route::get('team', [OperationsTeamController::class, 'index'])->name('team.index');

    Route::get('reports', [OperationsPeriodReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [OperationsPeriodReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{operationsPeriodReport}', [OperationsPeriodReportController::class, 'show'])->name('reports.show');
    Route::put('reports/{operationsPeriodReport}', [OperationsPeriodReportController::class, 'update'])->name('reports.update');
    Route::post('reports/{operationsPeriodReport}/refresh', [OperationsPeriodReportController::class, 'refresh'])->name('reports.refresh');
    Route::post('reports/{operationsPeriodReport}/submit', [OperationsPeriodReportController::class, 'submit'])->name('reports.submit');
    Route::post('reports/{operationsPeriodReport}/annotate', [OperationsPeriodReportController::class, 'annotate'])->name('reports.annotate');

    Route::get('attendance-reviews', [OperationsAttendanceReviewController::class, 'index'])->name('attendance-reviews.index');
    Route::post('attendance-reviews/flag', [OperationsAttendanceReviewController::class, 'flagToday'])->name('attendance-reviews.flag');
    Route::post('attendance-reviews/{attendanceAbsenceReview}/confirm-absent', [OperationsAttendanceReviewController::class, 'confirmAbsent'])->name('attendance-reviews.confirm-absent');
    Route::post('attendance-reviews/{attendanceAbsenceReview}/confirm-present', [OperationsAttendanceReviewController::class, 'confirmPresent'])->name('attendance-reviews.confirm-present');
    Route::post('attendance-reviews/{attendanceAbsenceReview}/excuse', [OperationsAttendanceReviewController::class, 'excuse'])->name('attendance-reviews.excuse');
    Route::post('attendance-reviews/{attendanceAbsenceReview}/revoke', [OperationsAttendanceReviewController::class, 'revoke'])->name('attendance-reviews.revoke');

    Route::get('checkout-reviews', [OperationsCheckoutReviewController::class, 'index'])->name('checkout-reviews.index');
    Route::post('checkout-reviews/{checkoutReview}/approve', [OperationsCheckoutReviewController::class, 'approve'])->name('checkout-reviews.approve');
    Route::post('checkout-reviews/{checkoutReview}/reject', [OperationsCheckoutReviewController::class, 'reject'])->name('checkout-reviews.reject');
    Route::post('checkout-reviews/{checkoutReview}/revoke', [OperationsCheckoutReviewController::class, 'revoke'])->name('checkout-reviews.revoke');

    Route::get('leaves', [OperationsLeaveController::class, 'index'])->name('leaves.index');
    Route::post('leaves/{leave}/approve', [OperationsLeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('leaves/{leave}/reject', [OperationsLeaveController::class, 'reject'])->name('leaves.reject');

    Route::get('exit-permits', [OperationsExitPermitController::class, 'index'])->name('exit-permits.index');
    Route::post('exit-permits/{exitPermit}/approve', [OperationsExitPermitController::class, 'approve'])->name('exit-permits.approve');
    Route::post('exit-permits/{exitPermit}/reject', [OperationsExitPermitController::class, 'reject'])->name('exit-permits.reject');
});
