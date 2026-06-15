<?php

use App\Http\Controllers\Hr\HrAbsenceController;
use App\Http\Controllers\Hr\HrCustodyController;
use App\Http\Controllers\Hr\HrDashboardController;
use App\Http\Controllers\Hr\HrEmployeeContractController;
use App\Http\Controllers\Hr\HrEmployeeDocumentController;
use App\Http\Controllers\Hr\HrExitPermitController;
use App\Http\Controllers\Hr\HrMonthlyReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/', [HrDashboardController::class, 'index'])->name('dashboard');

    Route::get('exit-permits', [HrExitPermitController::class, 'index'])->name('exit-permits.index');
    Route::post('exit-permits', [HrExitPermitController::class, 'store'])->name('exit-permits.store');
    Route::post('exit-permits/{exitPermit}/approve', [HrExitPermitController::class, 'approve'])->name('exit-permits.approve');
    Route::post('exit-permits/{exitPermit}/reject', [HrExitPermitController::class, 'reject'])->name('exit-permits.reject');

    Route::get('absences', [HrAbsenceController::class, 'index'])->name('absences.index');
    Route::post('absences/flag', [HrAbsenceController::class, 'flagToday'])->name('absences.flag');
    Route::post('absences/{attendanceAbsenceReview}/confirm-absent', [HrAbsenceController::class, 'confirmAbsent'])->name('absences.confirm-absent');
    Route::post('absences/{attendanceAbsenceReview}/confirm-present', [HrAbsenceController::class, 'confirmPresent'])->name('absences.confirm-present');
    Route::post('absences/{attendanceAbsenceReview}/excuse', [HrAbsenceController::class, 'excuse'])->name('absences.excuse');
    Route::post('absences/{attendanceAbsenceReview}/revoke', [HrAbsenceController::class, 'revoke'])->name('absences.revoke');

    Route::get('reports/monthly', [HrMonthlyReportController::class, 'index'])->name('reports.monthly');
    Route::get('reports/monthly/print', [HrMonthlyReportController::class, 'print'])->name('reports.monthly.print');

    Route::get('contracts', [HrEmployeeContractController::class, 'index'])->name('contracts.index');
    Route::post('contracts', [HrEmployeeContractController::class, 'store'])->name('contracts.store');
    Route::put('contracts/{employeeContract}', [HrEmployeeContractController::class, 'update'])->name('contracts.update');
    Route::get('contracts/{employeeContract}/download', [HrEmployeeContractController::class, 'download'])->name('contracts.download');
    Route::delete('contracts/{employeeContract}', [HrEmployeeContractController::class, 'destroy'])->name('contracts.destroy');

    Route::get('custody', [HrCustodyController::class, 'index'])->name('custody.index');
    Route::post('custody', [HrCustodyController::class, 'store'])->name('custody.store');
    Route::post('custody/{custodyAssignment}/return', [HrCustodyController::class, 'returnItem'])->name('custody.return');
    Route::get('custody/{custodyAssignment}/issue-file', [HrCustodyController::class, 'downloadIssueFile'])->name('custody.issue-file');
    Route::get('custody/{custodyAssignment}/return-file', [HrCustodyController::class, 'downloadReturnFile'])->name('custody.return-file');

    Route::get('documents', [HrEmployeeDocumentController::class, 'index'])->name('documents.index');
    Route::post('documents', [HrEmployeeDocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{employeeDocument}/download', [HrEmployeeDocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{employeeDocument}', [HrEmployeeDocumentController::class, 'destroy'])->name('documents.destroy');
});
