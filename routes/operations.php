<?php

use App\Http\Controllers\Operations\OperationsAttendanceReviewController;
use App\Http\Controllers\Operations\OperationsCrmController;
use App\Http\Controllers\Operations\OperationsDashboardController;
use App\Http\Controllers\Operations\OperationsInventoryController;
use App\Http\Controllers\Operations\OperationsLeadController;
use App\Http\Controllers\Operations\OperationsPeriodReportController;
use App\Http\Controllers\Operations\OperationsTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('operations')->name('operations.')->group(function () {
    Route::get('/', [OperationsDashboardController::class, 'index'])->name('dashboard');

    Route::get('leads', [OperationsLeadController::class, 'index'])->name('leads.index');
    Route::post('leads/{client}/assign', [OperationsLeadController::class, 'assign'])->name('leads.assign');
    Route::post('leads/distribute-batch', [OperationsLeadController::class, 'distributeBatch'])->name('leads.distribute-batch');
    Route::post('leads/auto-distribute', [OperationsLeadController::class, 'autoDistribute'])->name('leads.auto-distribute');

    Route::get('crm', [OperationsCrmController::class, 'index'])->name('crm.index');
    Route::get('inventory', [OperationsInventoryController::class, 'index'])->name('inventory.index');
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
});
