<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\ExitPermit;
use App\Services\Hr\ExitPermitScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsExitPermitController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $scope = ExitPermitScopeService::for(Auth::user());

        $permits = $scope->permitsQuery()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('permit_type'), fn ($q) => $q->where('permit_type', $request->permit_type))
            ->when($request->filled('month'), function ($q) use ($request) {
                $month = Carbon::parse($request->month . '-01');
                $q->whereBetween('permit_date', [
                    $month->copy()->startOfMonth()->toDateString(),
                    $month->copy()->endOfMonth()->toDateString(),
                ]);
            })
            ->orderByDesc('permit_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('operations.exit-permits.index', [
            'permits' => $permits,
            'scope' => $scope,
            'stats' => $scope->stats(),
            'permitTypes' => config('exit_permits.types', []),
        ]);
    }

    public function approve(ExitPermit $exitPermit)
    {
        $scope = ExitPermitScopeService::for(Auth::user());

        if (!$scope->canApprovePermit($exitPermit)) {
            abort(403);
        }

        $exitPermit->update([
            'status' => ExitPermit::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'تم اعتماد الإذن.');
    }

    public function reject(Request $request, ExitPermit $exitPermit)
    {
        $scope = ExitPermitScopeService::for(Auth::user());

        if (!$scope->canApprovePermit($exitPermit)) {
            abort(403);
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $exitPermit->update([
            'status' => ExitPermit::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'تم رفض الإذن.');
    }
}
