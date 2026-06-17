<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ClientChangeRequest;
use App\Services\ClientApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmClientApprovalController extends Controller
{
    public function __construct(protected ClientApprovalService $approval) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->approval->canApprove($user)) {
            $query = ClientChangeRequest::query()
                ->with(['requester', 'client', 'reviewer'])
                ->where('action', '!=', ClientChangeRequest::ACTION_CREATE)
                ->orderByDesc('created_at');

            if ($request->status) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', ClientChangeRequest::STATUS_PENDING);
            }

            $requests = $query->paginate(20)->withQueryString();
            $stats = [
                'pending' => ClientChangeRequest::where('status', ClientChangeRequest::STATUS_PENDING)
                    ->where('action', '!=', ClientChangeRequest::ACTION_CREATE)
                    ->count(),
                'approved' => ClientChangeRequest::where('status', ClientChangeRequest::STATUS_APPROVED)->whereMonth('reviewed_at', now()->month)->count(),
                'rejected' => ClientChangeRequest::where('status', ClientChangeRequest::STATUS_REJECTED)->whereMonth('reviewed_at', now()->month)->count(),
            ];

            return view('crm.clients.approvals.index', compact('requests', 'stats'));
        }

        $requests = ClientChangeRequest::query()
            ->with(['client', 'reviewer'])
            ->where('requested_by', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('crm.clients.my-requests', compact('requests'));
    }

    public function show(ClientChangeRequest $changeRequest)
    {
        $user = Auth::user();

        if (!$this->approval->canApprove($user) && (int) $changeRequest->requested_by !== (int) $user->id) {
            abort(403);
        }

        $changeRequest->load(['requester', 'client', 'reviewer']);

        return view('crm.clients.approvals.show', [
            'request' => $changeRequest,
            'canApprove' => $this->approval->canApprove($user)
                && $changeRequest->status === ClientChangeRequest::STATUS_PENDING
                && (int) $changeRequest->requested_by !== (int) $user->id,
        ]);
    }

    public function approve(Request $request, ClientChangeRequest $changeRequest)
    {
        $client = $this->approval->approve($changeRequest, Auth::user(), $request->input('review_notes'));

        if ($client) {
            return redirect()->route('crm.clients.show', $client)
                ->with('success', 'تمت الموافقة وتنفيذ الطلب بنجاح.');
        }

        return redirect()->route('crm.clients.index')
            ->with('success', 'تمت الموافقة على حذف العميل.');
    }

    public function reject(Request $request, ClientChangeRequest $changeRequest)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);

        $this->approval->reject($changeRequest, Auth::user(), $request->input('review_notes'));

        return redirect()->route('crm.clients.approvals.index')
            ->with('success', 'تم رفض الطلب.');
    }
}
