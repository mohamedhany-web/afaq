<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmFollowUp;
use App\Services\CrmFollowUpService;
use App\Services\FollowUpDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmFollowUpController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $workspace = $user->hasRole(['super_admin', 'admin']) ? 'admin' : 'sales';

        return view('crm.follow-ups.index', FollowUpDashboardService::for($user)->buildIndex($request, $user, $workspace));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'user_id' => 'nullable|exists:users,id',
            'sale_id' => 'nullable|exists:sales,id',
            'interaction_type' => 'required|in:' . implode(',', CrmFollowUp::TYPES),
            'notes' => 'required|string|max:5000',
            'scheduled_at' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $validated['scheduled_at'] = $validated['scheduled_at'] . ' ' . $validated['scheduled_time'];

        $followUp = CrmFollowUpService::for(Auth::user())->create($validated, Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الموعد في الجدول',
                'follow_up' => $followUp,
            ]);
        }

        $scheduledDate = Carbon::parse($validated['scheduled_at'])->toDateString();
        $redirectRoute = $request->input('_redirect_route', 'crm.follow-ups.index');

        return redirect()
            ->route($redirectRoute, [
                'date' => $scheduledDate,
                'highlight' => $followUp->id,
            ])
            ->with('success', 'تم تسجيل المتابعة في الجدول — يوم ' . Carbon::parse($scheduledDate)->locale('ar')->translatedFormat('j F Y'));
    }

    public function complete(CrmFollowUp $followUp)
    {
        CrmFollowUpService::for(Auth::user())->complete($followUp, Auth::user());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم إكمال المتابعة');
    }

    public function cancel(CrmFollowUp $followUp)
    {
        CrmFollowUpService::for(Auth::user())->cancel($followUp, Auth::user());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم إلغاء الموعد');
    }
}
