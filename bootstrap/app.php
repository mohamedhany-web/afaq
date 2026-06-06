<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'verified.code' => \App\Http\Middleware\EnsureVerifiedCode::class,
            'secure.auth' => \App\Http\Middleware\SecureAuth::class,
            'block.suspicious' => \App\Http\Middleware\BlockSuspiciousActivity::class,
            'department.manager' => \App\Http\Middleware\EnsureDepartmentManager::class,
            'client.section' => \App\Http\Middleware\EnsureClientPortalSection::class,
            'crm.only' => \App\Http\Middleware\EnsureCrmOnlyAccess::class,
            'marketing.only' => \App\Http\Middleware\EnsureMarketingOnlyAccess::class,
            'crm.rep' => \App\Http\Middleware\EnsureCrmSalesRep::class,
            'redirect.crm' => \App\Http\Middleware\RedirectToCrmDashboard::class,
        ]);
        
        // Global security middleware
        $middleware->web([
            \App\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\SecureAuth::class,
            \App\Http\Middleware\BlockSuspiciousActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
