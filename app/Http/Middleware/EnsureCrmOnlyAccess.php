<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCrmOnlyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isCrmOnlyUser()) {
            return $next($request);
        }

        $allowedPrefixes = [
            'crm',
            'api',
            'profile',
            'logout',
            'verify-code',
            'resend-code',
            'storage',
            'notifications',
            'messages',
            'attendances',
            'leaves',
            'hr/exit-permits',
        ];
        $path = trim($request->path(), '/');

        foreach ($allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        if ($request->routeIs('logout', 'profile.*', 'verification.*', 'notifications.*', 'messages.*', 'attendances.*', 'leaves.*', 'hr.exit-permits.*')) {
            return $next($request);
        }

        return redirect()->route('crm.dashboard');
    }
}
