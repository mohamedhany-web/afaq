<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketingOnlyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isMarketingOnlyUser()) {
            return $next($request);
        }

        $allowedPrefixes = [
            'marketing',
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
        ];

        $path = trim($request->path(), '/');

        foreach ($allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        if ($request->routeIs('logout', 'profile.*', 'verification.*', 'notifications.*', 'messages.*', 'attendances.*', 'leaves.*', 'dashboard')) {
            return $next($request);
        }

        return redirect()->route('marketing.dashboard');
    }
}
