<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\ExitPermit;
use App\Services\Hr\ExitPermitScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrExitPermitController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user?->canAccessHr() && !ExitPermitScopeService::for($user)->canRequest()) {
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

        return view('hr.exit-permits.index', [
            'permits' => $permits,
            'scope' => $scope,
            'mode' => $scope->mode(),
            'stats' => $scope->stats(),
            'permitTypes' => config('exit_permits.types', []),
            'employee' => $scope->employee(),
        ]);
    }

    public function store(Request $request)
    {
        $scope = ExitPermitScopeService::for(Auth::user());

        if (!$scope->canRequest()) {
            return response()->json(['error' => 'غير مصرح لك بتقديم إذن'], 403);
        }

        $request->validate([
            'permit_type' => 'required|string|in:' . implode(',', array_keys(config('exit_permits.types', []))),
            'permit_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'reason' => 'required|string|max:500',
        ]);

        $employee = $scope->employee();
        if (!$employee) {
            return response()->json(['error' => 'لا يوجد ملف موظف مرتبط بحسابك'], 404);
        }

        $permit = ExitPermit::create([
            'employee_id' => $employee->id,
            'permit_date' => $request->permit_date,
            'permit_type' => $request->permit_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'reason' => $request->reason,
            'status' => ExitPermit::STATUS_PENDING,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'permit' => $permit]);
        }

        return back()->with('success', 'تم تقديم طلب الإذن بنجاح.');
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
