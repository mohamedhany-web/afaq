<?php

namespace App\Http\Middleware;

use App\Services\CrmRoleResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCrmSalesRep
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !CrmRoleResolver::for($user)->canCreateDailySalesReport()) {
            abort(403, 'هذه العملية متاحة لموظفي المبيعات فقط.');
        }

        return $next($request);
    }
}
