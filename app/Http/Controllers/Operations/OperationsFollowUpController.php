<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Crm\CrmFollowUpController;
use App\Models\CrmFollowUp;
use App\Services\CrmFollowUpService;
use App\Services\FollowUpDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsFollowUpController extends Controller
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
        $user = Auth::user();

        return view('operations.follow-ups.index', FollowUpDashboardService::for($user)->buildIndex($request, $user, 'operations'));
    }

    public function store(Request $request)
    {
        $request->merge(['_redirect_route' => 'operations.follow-ups.index']);

        return app(CrmFollowUpController::class)->store($request);
    }

    public function complete(CrmFollowUp $followUp)
    {
        CrmFollowUpService::for(Auth::user())->complete($followUp, Auth::user());

        return redirect()
            ->route('operations.follow-ups.index', request()->only(['date', 'bucket', 'search', 'sales_rep', 'status']))
            ->with('success', 'تم إكمال المتابعة');
    }

    public function cancel(CrmFollowUp $followUp)
    {
        CrmFollowUpService::for(Auth::user())->cancel($followUp, Auth::user());

        return redirect()
            ->route('operations.follow-ups.index', request()->only(['date', 'bucket', 'search', 'sales_rep', 'status']))
            ->with('success', 'تم إلغاء الموعد');
    }
}
