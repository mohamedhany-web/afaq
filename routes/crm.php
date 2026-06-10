<?php

use App\Http\Controllers\ClientSearchController;
use App\Http\Controllers\DeveloperSearchController;
use App\Http\Controllers\Crm\CrmDailySalesReportController;
use App\Http\Controllers\Crm\CrmDashboardController;
use App\Http\Controllers\Crm\CrmClientApprovalController;
use App\Http\Controllers\Crm\CrmClientController;
use App\Http\Controllers\Crm\CrmFollowUpController;
use App\Http\Controllers\Crm\CrmIntelligenceController;
use App\Http\Controllers\Crm\CrmPipelineController;
use App\Http\Controllers\Crm\CrmProjectApprovalController;
use App\Http\Controllers\Crm\CrmProjectController;
use App\Http\Controllers\Crm\CrmProjectUnitController;
use App\Http\Controllers\Crm\CrmTeamMemberController;
use App\Http\Controllers\Crm\Compensation\CompAdjustmentController;
use App\Http\Controllers\Crm\Compensation\CompEmployeeCompensationController;
use App\Http\Controllers\Crm\Compensation\CompensationDashboardController;
use App\Http\Controllers\Crm\Compensation\CompensationReportController;
use App\Http\Controllers\Crm\Compensation\CompKpiTemplateController;
use App\Http\Controllers\Crm\Compensation\CompPayrollController;
use App\Http\Controllers\Crm\CrmTaskController;
use App\Http\Controllers\Crm\EmployeeComplianceController;
use App\Http\Controllers\Crm\FreelanceAgentController;
use App\Http\Controllers\Crm\SalesTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('clients/search', ClientSearchController::class)->name('clients.search');
    Route::get('developers/search', DeveloperSearchController::class)->name('developers.search');

    Route::get('/', [CrmDashboardController::class, 'index'])->name('dashboard');
    Route::get('intelligence', [CrmIntelligenceController::class, 'index'])->name('intelligence.index');

    Route::get('clients', [CrmClientController::class, 'index'])->name('clients.index');
    Route::get('clients/approvals/list', [CrmClientApprovalController::class, 'index'])->name('clients.approvals.index');
    Route::get('clients/approvals/{changeRequest}', [CrmClientApprovalController::class, 'show'])->name('clients.approvals.show');
    Route::post('clients/approvals/{changeRequest}/approve', [CrmClientApprovalController::class, 'approve'])->name('clients.approvals.approve');
    Route::post('clients/approvals/{changeRequest}/reject', [CrmClientApprovalController::class, 'reject'])->name('clients.approvals.reject');
    Route::get('clients/create', [CrmClientController::class, 'create'])->name('clients.create');
    Route::get('clients/import/template', [CrmClientController::class, 'importTemplate'])->name('clients.import.template');
    Route::post('clients/import', [CrmClientController::class, 'import'])->name('clients.import');
    Route::post('clients', [CrmClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [CrmClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [CrmClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [CrmClientController::class, 'update'])->name('clients.update');
    Route::patch('clients/{client}/lead-stage', [CrmClientController::class, 'updateLeadStage'])->name('clients.update-lead-stage');
    Route::post('clients/{client}/interaction', [CrmClientController::class, 'logInteraction'])->name('clients.log-interaction');
    Route::delete('clients/{client}', [CrmClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('daily-reports', [CrmDailySalesReportController::class, 'index'])->name('daily-reports.index');
    Route::get('daily-reports/{dailySalesReport}', [CrmDailySalesReportController::class, 'show'])->name('daily-reports.show');

    Route::middleware('crm.rep')->group(function () {
        Route::post('daily-reports/generate', [CrmDailySalesReportController::class, 'generate'])->name('daily-reports.generate');
        Route::put('daily-reports/{dailySalesReport}', [CrmDailySalesReportController::class, 'update'])->name('daily-reports.update');
        Route::post('daily-reports/{dailySalesReport}/refresh', [CrmDailySalesReportController::class, 'refresh'])->name('daily-reports.refresh');
        Route::post('daily-reports/{dailySalesReport}/submit', [CrmDailySalesReportController::class, 'submit'])->name('daily-reports.submit');
    });

    Route::get('tasks', [CrmTaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [CrmTaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [CrmTaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}', [CrmTaskController::class, 'show'])->name('tasks.show');
    Route::get('tasks/{task}/edit', [CrmTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [CrmTaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks/{task}/accept', [CrmTaskController::class, 'accept'])->name('tasks.accept');
    Route::post('tasks/{task}/start', [CrmTaskController::class, 'start'])->name('tasks.start');
    Route::post('tasks/{task}/complete', [CrmTaskController::class, 'complete'])->name('tasks.complete');
    Route::post('tasks/{task}/verify', [CrmTaskController::class, 'verify'])->name('tasks.verify');
    Route::post('tasks/{task}/cancel', [CrmTaskController::class, 'cancel'])->name('tasks.cancel');

    Route::get('schedule', [CrmFollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('schedule', [CrmFollowUpController::class, 'store'])->name('follow-ups.store');
    Route::patch('schedule/{followUp}/complete', [CrmFollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::patch('schedule/{followUp}/cancel', [CrmFollowUpController::class, 'cancel'])->name('follow-ups.cancel');

    Route::get('team/members/{member}', [CrmTeamMemberController::class, 'show'])->name('team-members.show');

    Route::get('pipeline', [CrmPipelineController::class, 'index'])->name('pipeline.index');
    Route::get('pipeline/create', [CrmPipelineController::class, 'create'])->name('pipeline.create');
    Route::post('pipeline', [CrmPipelineController::class, 'store'])->name('pipeline.store');
    Route::get('pipeline/clients/{client}', [CrmPipelineController::class, 'showClient'])->name('pipeline.client');
    Route::get('pipeline/column/{stage}/deals', [CrmPipelineController::class, 'columnDeals'])->name('pipeline.column-deals');
    Route::get('pipeline/{sale}', [CrmPipelineController::class, 'show'])->name('pipeline.show');
    Route::get('pipeline/{sale}/edit', [CrmPipelineController::class, 'edit'])->name('pipeline.edit');
    Route::put('pipeline/{sale}', [CrmPipelineController::class, 'update'])->name('pipeline.update');
    Route::patch('pipeline/{sale}/stage', [CrmPipelineController::class, 'updateStage'])->name('pipeline.update-stage');

    Route::get('projects', [CrmProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [CrmProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [CrmProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/approvals/list', [CrmProjectApprovalController::class, 'index'])->name('projects.approvals.index');
    Route::get('projects/approvals/{changeRequest}', [CrmProjectApprovalController::class, 'show'])->name('projects.approvals.show');
    Route::post('projects/approvals/{changeRequest}/approve', [CrmProjectApprovalController::class, 'approve'])->name('projects.approvals.approve');
    Route::post('projects/approvals/{changeRequest}/reject', [CrmProjectApprovalController::class, 'reject'])->name('projects.approvals.reject');
    Route::get('projects/{project}', [CrmProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [CrmProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [CrmProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [CrmProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('projects/{project}/units/generate', [CrmProjectUnitController::class, 'generate'])->name('projects.units.generate');
    Route::patch('projects/{project}/units/{unit}', [CrmProjectUnitController::class, 'update'])->name('projects.units.update');

    Route::get('teams', [SalesTeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [SalesTeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [SalesTeamController::class, 'store'])->name('teams.store');
    Route::get('teams/{team}', [SalesTeamController::class, 'show'])->name('teams.show');
    Route::get('teams/{team}/edit', [SalesTeamController::class, 'edit'])->name('teams.edit');
    Route::put('teams/{team}', [SalesTeamController::class, 'update'])->name('teams.update');
    Route::delete('teams/{team}', [SalesTeamController::class, 'destroy'])->name('teams.destroy');

    Route::prefix('compensation')->name('compensation.')->group(function () {
        Route::get('/', [CompensationDashboardController::class, 'index'])->name('dashboard');
        Route::post('payroll/recalculate', [CompPayrollController::class, 'recalculate'])->name('payroll.recalculate');
        Route::get('payroll/{run}', [CompPayrollController::class, 'show'])->name('payroll.show');
        Route::post('payroll/{run}/approve', [CompPayrollController::class, 'approve'])->name('payroll.approve');
        Route::post('adjustments', [CompAdjustmentController::class, 'store'])->name('adjustments.store');
        Route::post('adjustments/{adjustment}/review', [CompAdjustmentController::class, 'review'])->name('adjustments.review');

        Route::get('kpi-templates', [CompKpiTemplateController::class, 'index'])->name('kpi.index');
        Route::get('kpi-templates/create', [CompKpiTemplateController::class, 'create'])->name('kpi.create');
        Route::post('kpi-templates', [CompKpiTemplateController::class, 'store'])->name('kpi.store');
        Route::get('kpi-templates/{template}/edit', [CompKpiTemplateController::class, 'edit'])->name('kpi.edit');
        Route::put('kpi-templates/{template}', [CompKpiTemplateController::class, 'update'])->name('kpi.update');
        Route::post('kpi-templates/{template}/assign', [CompKpiTemplateController::class, 'assign'])->name('kpi.assign');

        Route::get('profiles', [CompEmployeeCompensationController::class, 'index'])->name('profiles.index');
        Route::post('profiles', [CompEmployeeCompensationController::class, 'store'])->name('profiles.store');
        Route::put('profiles/{profile}', [CompEmployeeCompensationController::class, 'update'])->name('profiles.update');

        Route::get('reports', [CompensationReportController::class, 'index'])->name('reports.index');
    });

    Route::get('employee-compliance', [EmployeeComplianceController::class, 'index'])->name('employee-compliance.index');
    Route::get('employee-compliance/users/{user}', [EmployeeComplianceController::class, 'show'])->name('employee-compliance.show');

    Route::prefix('freelance-agents')->name('freelance-agents.')->group(function () {
        Route::get('scheme', [FreelanceAgentController::class, 'scheme'])->name('scheme');
        Route::get('/', [FreelanceAgentController::class, 'index'])->name('index');
        Route::get('create', [FreelanceAgentController::class, 'create'])->name('create');
        Route::post('/', [FreelanceAgentController::class, 'store'])->name('store');
        Route::get('{contract}', [FreelanceAgentController::class, 'show'])->name('show');
        Route::get('{contract}/edit', [FreelanceAgentController::class, 'edit'])->name('edit');
        Route::put('{contract}', [FreelanceAgentController::class, 'update'])->name('update');
        Route::get('{contract}/contract-print', [FreelanceAgentController::class, 'contractPrint'])->name('contract-print');
    });

    Route::post('pipeline/{sale}/commission-collected', [FreelanceAgentController::class, 'markCommissionCollected'])
        ->name('pipeline.commission-collected');
});
