<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperationsOnlyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isOperationsOnlyUser()) {
            return $next($request);
        }

        $allowedPrefixes = [
            'operations',
            'crm/clients',
            'crm/projects',
            'crm/schedule',
            'admin/developers',
            'admin/system-reports',
            'employees',
            'leaves',
            'attendances',
            'hr/exit-permits',
            'api',
            'profile',
            'logout',
            'verify-code',
            'resend-code',
            'storage',
            'notifications',
            'messages',
        ];

        $path = trim($request->path(), '/');

        foreach ($allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        if ($request->routeIs(
            'logout', 'profile.*', 'verification.*', 'notifications.*', 'messages.*',
            'attendances.*', 'leaves.*', 'employees.*', 'operations.*',
            'hr.exit-permits.*',
            'crm.clients.*', 'crm.projects.*', 'crm.follow-ups.*', 'admin.developers.*', 'admin.system-reports.*',
        )) {
            return $next($request);
        }

        return redirect()->route('operations.dashboard');
    }
}
