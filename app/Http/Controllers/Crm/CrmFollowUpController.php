<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Services\CrmFollowUpService;
use App\Services\CrmScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmFollowUpController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $service = CrmFollowUpService::for($user);
        $scope = CrmScopeService::for($user);

        $date = $request->filled('date') ? Carbon::parse($request->date) : now();
        $view = $request->get('view', 'day');

        $base = $service->followUpsQuery()
            ->with(['client:id,name,phone', 'user:id,name', 'creator:id,name', 'sale:id,product_service']);

        if ($view === 'week') {
            $start = $date->copy()->startOfWeek();
            $end = $date->copy()->endOfWeek();
        } else {
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
        }

        $query = (clone $base)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('interaction_type', $request->type))
            ->when($request->user_id && $scope->isManagerScope(), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('notes', 'like', $s)
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $s)->orWhere('phone', 'like', $s));
                });
            });

        $dayQuery = $view === 'week'
            ? (clone $query)->whereBetween('scheduled_at', [$start, $end])
            : (clone $query)->whereDate('scheduled_at', $date->toDateString());

        $stats = [
            'today' => (clone $base)->scheduled()->whereDate('scheduled_at', today())->count(),
            'overdue' => (clone $base)->scheduled()->where('scheduled_at', '<', now())->count(),
            'upcoming' => (clone $base)->scheduled()->whereBetween('scheduled_at', [now(), now()->addDays(7)])->count(),
        ];

        $followUps = $dayQuery->orderBy('scheduled_at')->paginate(30)->withQueryString();

        $assignableUsers = collect($service->assignableUsers($user));

        return view('crm.follow-ups.index', [
            'followUps' => $followUps,
            'stats' => $stats,
            'date' => $date,
            'view' => $view,
            'assignableUsers' => $assignableUsers,
            'typeLabels' => CrmFollowUp::TYPE_LABELS,
            'canAssignOthers' => $assignableUsers->count() > 1,
            'isManager' => $scope->isManagerScope() || $scope->hasFullAccess(),
            'highlight' => $request->integer('highlight'),
        ]);
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

        return redirect()
            ->route('crm.follow-ups.index', [
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
