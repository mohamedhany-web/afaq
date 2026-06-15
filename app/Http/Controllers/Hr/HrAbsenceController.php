<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceAbsenceReview;
use App\Services\AttendanceAbsenceReviewService;
use App\Services\OrganizationalHierarchyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrAbsenceController extends Controller
{
    public function __construct(
        protected AttendanceAbsenceReviewService $reviews,
        protected OrganizationalHierarchyService $hierarchy,
    ) {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user?->canAccessHr() && !$user?->can('view-attendance')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceAbsenceReview::class);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $query = AttendanceAbsenceReview::query()
            ->with(['employee.department', 'employee.user', 'lineManager', 'reviewer'])
            ->whereDate('review_date', $date);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->orderBy('status')->orderBy('employee_id')->paginate(30)->withQueryString();

        $stats = [
            'pending' => AttendanceAbsenceReview::whereDate('review_date', $date)->where('status', 'pending')->count(),
            'confirmed_absent' => AttendanceAbsenceReview::whereDate('review_date', $date)->whereIn('status', ['confirmed_absent', 'auto_confirmed'])->count(),
            'confirmed_present' => AttendanceAbsenceReview::whereDate('review_date', $date)->where('status', 'confirmed_present')->count(),
            'excused' => AttendanceAbsenceReview::whereDate('review_date', $date)->where('status', 'excused')->count(),
        ];

        return view('hr.absences.index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'date' => $date,
            'canReview' => $this->canReviewAbsences(),
        ]);
    }

    public function confirmAbsent(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorizeReview($attendanceAbsenceReview);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $this->reviews->confirmAbsent($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم تأكيد الغياب.');
    }

    public function confirmPresent(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorizeReview($attendanceAbsenceReview);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $this->reviews->confirmPresent($attendanceAbsenceReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم تأكيد الحضور وتحديث السجل.');
    }

    public function excuse(Request $request, AttendanceAbsenceReview $attendanceAbsenceReview)
    {
        $this->authorizeReview($attendanceAbsenceReview);

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

    public function flagToday()
    {
        $this->authorize('viewAny', AttendanceAbsenceReview::class);

        if (!$this->canReviewAbsences()) {
            abort(403);
        }

        $count = $this->reviews->flagAbsencesForDate(Carbon::yesterday());

        return back()->with('success', "تم تحديث قائمة الغياب — {$count} سجل جديد.");
    }

    protected function canReviewAbsences(): bool
    {
        return Auth::user()?->canAccessHr()
            || $this->hierarchy->canReviewAttendance(Auth::user());
    }

    protected function authorizeReview(AttendanceAbsenceReview $review): void
    {
        if (!$review->isPending()) {
            abort(403);
        }

        if (!$this->canReviewAbsences()) {
            abort(403);
        }
    }
}
