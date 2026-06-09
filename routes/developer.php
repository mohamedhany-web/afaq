<?php

use App\Http\Controllers\DeveloperAuthController;
use App\Http\Controllers\DeveloperPortal\DashboardController;
use App\Http\Controllers\DeveloperPortal\PortfolioController;
use App\Http\Controllers\DeveloperPortal\ProfileController;
use App\Http\Controllers\DeveloperPortal\ProjectController;
use App\Http\Controllers\DeveloperPortal\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('developer')->name('developer.')->group(function () {
    Route::get('login', [DeveloperAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [DeveloperAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [DeveloperAuthController::class, 'logout'])->name('logout');
});

Route::prefix('developer')->name('developer.')->middleware('auth:developer')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('projects/{project}/units/generate', [UnitController::class, 'generate'])->name('projects.units.generate');
    Route::patch('projects/{project}/units/{unit}', [UnitController::class, 'update'])->name('projects.units.update');

    Route::get('portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('portfolio/create', [PortfolioController::class, 'create'])->name('portfolio.create');
    Route::post('portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::get('portfolio/{item}/edit', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::put('portfolio/{item}', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('portfolio/{item}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});
