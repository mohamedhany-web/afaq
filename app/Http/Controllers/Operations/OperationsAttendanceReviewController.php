<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAbsenceReview;
use App\Services\AttendanceAbsenceReviewService;
use App\Services\OrganizationalHierarchyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsAttendanceReviewController extends Controller
{
    public function __construct(
        protected AttendanceAbsenceReviewService $reviews,
        protected OrganizationalHierarchyService $hierarchy,
    ) {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations() && !$this->hierarchy->canReviewAttendance(Auth::user())) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceAbsenceReview::class);

        $date = $this->reviews->resolveDisplayDate($request->query('date'));
        $status = $request->query('status');

        $reviews = AttendanceAbsenceReview::query()
            ->with(['employee.department', 'employee.user', 'lineManager', 'reviewer'])
            ->forReviewDate($date)
            ->withStatusFilter($status)
            ->orderBy('status')
            ->orderBy('employee_id')
            ->paginate(30)
            ->withQueryString();

        $stats = $this->statsForDate($date);

        return view('operations.attendance-reviews.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'date' => $date,
            'status' => $status,
            'hierarchy' => $this->hierarchy->hierarchyChart(),
        ]);
    }

    public function confirmAbsent(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorize('review', $attendanceAbsenceReview);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $this->reviews->confirmAbsent($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم تأكيد الغياب.');
    }

    public function confirmPresent(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorize('review', $attendanceAbsenceReview);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $this->reviews->confirmPresent($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم تأكيد الحضور وتحديث السجل.');
    }

    public function excuse(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorize('review', $attendanceAbsenceReview);

        $request->validate(['notes' => 'required|string|max:1000']);

        $this->reviews->excuse($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم تسجيل العذر — لن يُحتسب غياباً وتم إلغاء أي خصم مرتبط.');
    }

    public function revoke(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorize('revoke', $attendanceAbsenceReview);

        $request->validate(['notes' => 'required|string|max:1000']);

        $this->reviews->revokeConfirmation($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم إلغاء قرار الغياب وإعادة السجل للمراجعة.');
    }

    public function flagToday(Request $request)
    {
        $this->authorize('viewAny', AttendanceAbsenceReview::class);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : $this->reviews->resolveDisplayDate(null);

        $count = $this->reviews->flagAbsencesForDate($date);

        return back()->with('success', "تم تحديث قائمة الغياب لتاريخ {$date->format('Y-m-d')} — {$count} سجل جديد.");
    }

    /** @return array<string, int> */
    protected function statsForDate(Carbon $date): array
    {
        $base = AttendanceAbsenceReview::query()->forReviewDate($date);

        return [
            'pending' => (clone $base)->where('status', AttendanceAbsenceReview::STATUS_PENDING)->count(),
            'confirmed_absent' => (clone $base)->whereIn('status', [
                AttendanceAbsenceReview::STATUS_CONFIRMED_ABSENT,
                AttendanceAbsenceReview::STATUS_AUTO_CONFIRMED,
            ])->count(),
            'confirmed_present' => (clone $base)->where('status', AttendanceAbsenceReview::STATUS_CONFIRMED_PRESENT)->count(),
            'excused' => (clone $base)->where('status', AttendanceAbsenceReview::STATUS_EXCUSED)->count(),
            'total' => (clone $base)->count(),
        ];
    }
}
