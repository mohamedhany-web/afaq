<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrOnlyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isHrOnlyUser()) {
            return $next($request);
        }

        $allowedPrefixes = [
            'hr',
            'employees',
            'leaves',
            'attendances',
            'users',
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
            'attendances.*', 'leaves.*', 'employees.*', 'users.*', 'hr.*',
        )) {
            return $next($request);
        }

        return redirect()->route('hr.dashboard');
    }
}
