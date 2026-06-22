<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Services\LeaveScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsLeaveController extends Controller
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
        $scope = LeaveScopeService::for(Auth::user());

        $leaves = $scope->leavesQuery()
            ->with(['employee.user', 'employee.department', 'approvedBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('operations.leaves.index', [
            'leaves' => $leaves,
            'stats' => $scope->stats(),
            'leaveTypes' => config('leaves.types', []),
            'status' => $request->query('status'),
        ]);
    }

    public function approve(Leave $leave)
    {
        $scope = LeaveScopeService::for(Auth::user());

        if (!$scope->canApproveLeave($leave)) {
            return response()->json(['error' => 'غير مصرح بالموافقة على هذا الطلب'], 403);
        }

        if ($leave->status !== 'pending') {
            return response()->json(['error' => 'تمت معالجة هذا الطلب مسبقاً'], 400);
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تمت الموافقة على طلب الإجازة']);
        }

        return back()->with('success', 'تمت الموافقة على طلب الإجازة');
    }

    public function reject(Request $request, Leave $leave)
    {
        $scope = LeaveScopeService::for(Auth::user());

        if (!$scope->canApproveLeave($leave)) {
            return response()->json(['error' => 'غير مصرح برفض هذا الطلب'], 403);
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        if ($leave->status !== 'pending') {
            return response()->json(['error' => 'تمت معالجة هذا الطلب مسبقاً'], 400);
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم رفض طلب الإجازة']);
        }

        return back()->with('success', 'تم رفض طلب الإجازة');
    }
}
