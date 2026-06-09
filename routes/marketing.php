<?php

use App\Http\Controllers\Marketing\MarketingActivityController;
use App\Http\Controllers\Marketing\MarketingPlanController;
use App\Http\Controllers\Marketing\MarketingCampaignController;
use App\Http\Controllers\Marketing\MarketingDashboardController;
use App\Http\Controllers\Marketing\MarketingLeadController;
use App\Http\Controllers\Marketing\MarketingPeriodReportController;
use App\Http\Controllers\Marketing\MarketingReportController;
use App\Http\Controllers\Marketing\MarketingTeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'verified.code'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/', [MarketingDashboardController::class, 'index'])->name('dashboard');

    Route::get('campaigns', [MarketingCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('campaigns/create', [MarketingCampaignController::class, 'create'])->name('campaigns.create');
    Route::post('campaigns', [MarketingCampaignController::class, 'store'])->name('campaigns.store');
    Route::get('campaigns/{campaign}', [MarketingCampaignController::class, 'show'])->name('campaigns.show');
    Route::get('campaigns/{campaign}/edit', [MarketingCampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('campaigns/{campaign}', [MarketingCampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('campaigns/{campaign}', [MarketingCampaignController::class, 'destroy'])->name('campaigns.destroy');

    Route::get('plans', [MarketingPlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [MarketingPlanController::class, 'create'])->name('plans.create');
    Route::post('plans', [MarketingPlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}', [MarketingPlanController::class, 'show'])->name('plans.show');
    Route::get('plans/{plan}/edit', [MarketingPlanController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [MarketingPlanController::class, 'update'])->name('plans.update');
    Route::post('plans/{plan}/tasks', [MarketingPlanController::class, 'storeTasks'])->name('plans.tasks.store');
    Route::post('plans/{plan}/distribute', [MarketingPlanController::class, 'distribute'])->name('plans.distribute');
    Route::post('plans/{plan}/activate', [MarketingPlanController::class, 'activate'])->name('plans.activate');

    Route::get('activities', [MarketingActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/create', [MarketingActivityController::class, 'create'])->name('activities.create');
    Route::post('activities', [MarketingActivityController::class, 'store'])->name('activities.store');
    Route::get('activities/{activity}/edit', [MarketingActivityController::class, 'edit'])->name('activities.edit');
    Route::put('activities/{activity}', [MarketingActivityController::class, 'update'])->name('activities.update');
    Route::patch('activities/{activity}/status', [MarketingActivityController::class, 'updateStatus'])->name('activities.update-status');
    Route::delete('activities/{activity}', [MarketingActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('leads', [MarketingLeadController::class, 'index'])->name('leads.index');
    Route::get('leads/create', [MarketingLeadController::class, 'create'])->name('leads.create');
    Route::post('leads', [MarketingLeadController::class, 'store'])->name('leads.store');

    Route::get('analytics', [MarketingReportController::class, 'index'])->name('analytics.index');

    Route::get('reports', [MarketingPeriodReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [MarketingPeriodReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{marketingPeriodReport}', [MarketingPeriodReportController::class, 'show'])->name('reports.show');
    Route::put('reports/{marketingPeriodReport}', [MarketingPeriodReportController::class, 'update'])->name('reports.update');
    Route::post('reports/{marketingPeriodReport}/refresh', [MarketingPeriodReportController::class, 'refresh'])->name('reports.refresh');
    Route::post('reports/{marketingPeriodReport}/submit', [MarketingPeriodReportController::class, 'submit'])->name('reports.submit');

    Route::get('team', [MarketingTeamController::class, 'index'])->name('team.index');
});
