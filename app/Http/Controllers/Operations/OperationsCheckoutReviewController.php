<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCheckoutReview;
use App\Services\AttendanceCheckoutReviewService;
use App\Services\OrganizationalHierarchyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsCheckoutReviewController extends Controller
{
    public function __construct(
        protected AttendanceCheckoutReviewService $checkouts,
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
        $this->authorize('viewAny', AttendanceCheckoutReview::class);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today()->startOfDay();

        $query = AttendanceCheckoutReview::query()
            ->with(['employee.department', 'employee.user', 'reviewer', 'attendance'])
            ->whereDate('review_date', $date);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', AttendanceCheckoutReview::STATUS_PENDING);
        }

        $reviews = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        $stats = [
            'pending' => AttendanceCheckoutReview::whereDate('review_date', $date)->where('status', 'pending')->count(),
            'approved' => AttendanceCheckoutReview::whereDate('review_date', $date)->where('status', 'approved')->count(),
            'rejected' => AttendanceCheckoutReview::whereDate('review_date', $date)->where('status', 'rejected')->count(),
            'revoked' => AttendanceCheckoutReview::whereDate('review_date', $date)->where('status', 'revoked')->count(),
        ];

        return view('operations.checkout-reviews.index', compact('reviews', 'stats', 'date'));
    }

    public function approve(Request $request, AttendanceCheckoutReview $checkoutReview)
    {
        $this->authorize('review', $checkoutReview);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $this->checkouts->approve($checkoutReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم اعتماد الانصراف وتسجيله.');
    }

    public function reject(Request $request, AttendanceCheckoutReview $checkoutReview)
    {
        $this->authorize('review', $checkoutReview);

        $request->validate(['notes' => 'required|string|max:1000']);

        $this->checkouts->reject($checkoutReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم رفض طلب الانصراف — يمكن للموظف إعادة الطلب. تم احتساب الخصم إن وُجد انصراف مبكر.');
    }

    public function revoke(Request $request, AttendanceCheckoutReview $checkoutReview)
    {
        $this->authorize('revoke', $checkoutReview);

        $request->validate(['notes' => 'required|string|max:1000']);

        $this->checkouts->revoke($checkoutReview, Auth::user(), $request->notes);

        return back()->with('success', 'تم إلغاء اعتماد الانصراف وإلغاء الخصم المرتبط إن وُجد.');
    }
}
