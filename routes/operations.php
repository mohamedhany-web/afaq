<?php

use App\Http\Controllers\Operations\OperationsAttendanceReviewController;
use App\Http\Controllers\Operations\OperationsCheckoutReviewController;
use App\Http\Controllers\Operations\OperationsClientController;
use App\Http\Controllers\Operations\OperationsCrmController;
use App\Http\Controllers\Operations\OperationsDashboardController;
use App\Http\Controllers\Operations\OperationsFollowUpController;
use App\Http\Controllers\Operations\OperationsInventoryController;
use App\Http\Controllers\Operations\OperationsLeadController;
use App\Http\Controllers\Operations\OperationsRepController;
use App\Http\Controllers\Operations\OperationsPeriodReportController;
use App\Http\Controllers\Operations\OperationsTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('operations')->name('operations.')->group(function () {
    Route::get('/', [OperationsDashboardController::class, 'index'])->name('dashboard');

    Route::get('leads', [OperationsLeadController::class, 'index'])->name('leads.index');
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
    Route::post('attendance-reviews/{attendanceAbsenceReview}/revoke', [OperationsAttendanceReviewController::class, 'revoke'])->name('attendance-reviews.revoke');

    Route::get('checkout-reviews', [OperationsCheckoutReviewController::class, 'index'])->name('checkout-reviews.index');
    Route::post('checkout-reviews/{checkoutReview}/approve', [OperationsCheckoutReviewController::class, 'approve'])->name('checkout-reviews.approve');
    Route::post('checkout-reviews/{checkoutReview}/reject', [OperationsCheckoutReviewController::class, 'reject'])->name('checkout-reviews.reject');
    Route::post('checkout-reviews/{checkoutReview}/revoke', [OperationsCheckoutReviewController::class, 'revoke'])->name('checkout-reviews.revoke');
});
