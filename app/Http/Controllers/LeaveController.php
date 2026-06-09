<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Services\LeaveScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $scope = LeaveScopeService::for(Auth::user());
        $employee = $scope->employee();
        $mode = $scope->mode();

        $leaves = $scope->leavesQuery()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('leaves.index', [
            'leaves' => $leaves,
            'employee' => $employee,
            'mode' => $mode,
            'stats' => $scope->stats(),
            'canApprove' => $scope->canApprove(),
            'leaveTypes' => config('leaves.types', []),
            'canRequest' => $employee && Auth::user()->can('create-leaves'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|in:' . implode(',', array_keys(config('leaves.types', []))),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $scope = LeaveScopeService::for(Auth::user());
        $employee = $scope->employee();

        if (!$employee) {
            return response()->json(['error' => 'لا يوجد ملف موظف مرتبط بحسابك. تواصل مع الإدارة.'], 404);
        }

        if (!Auth::user()->can('create-leaves')) {
            return response()->json(['error' => 'غير مصرح لك بتقديم طلب إجازة'], 403);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        if (Leave::where('employee_id', $employee->id)
            ->where('start_date', $startDate->toDateString())
            ->where('end_date', $endDate->toDateString())
            ->where('status', 'pending')
            ->where('leave_type', $request->leave_type)
            ->exists()) {
            return response()->json(['error' => 'يوجد طلب إجازة مقدم مسبقاً لهذه التواريخ'], 400);
        }

        if ($request->leave_type === 'annual' && $totalDays > $scope->remainingAnnualDays($employee)) {
            return response()->json(['error' => 'رصيد الإجازة السنوية غير كافٍ'], 400);
        }

        DB::beginTransaction();
        try {
            $leave = Leave::create([
                'employee_id' => $employee->id,
                'leave_type' => $request->leave_type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'pending',
                'applied_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تقديم طلب الإجازة بنجاح وهو بانتظار موافقة الإدارة',
                'leave' => $leave,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'حدث خطأ أثناء تقديم الطلب'], 500);
        }
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

        return response()->json(['success' => true, 'message' => 'تمت الموافقة على طلب الإجازة']);
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

        return response()->json(['success' => true, 'message' => 'تم رفض طلب الإجازة']);
    }

    public function create()
    {
        return redirect()->route('leaves.index');
    }

    public function show(Leave $leave)
    {
        return redirect()->route('leaves.index');
    }

    public function edit(Leave $leave)
    {
        return redirect()->route('leaves.index');
    }

    public function update(Request $request, Leave $leave)
    {
        return redirect()->route('leaves.index');
    }

    public function destroy(Leave $leave)
    {
        return redirect()->route('leaves.index');
    }
}
