<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ProjectChangeRequest;
use App\Services\ProjectApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmProjectApprovalController extends Controller
{
    public function __construct(protected ProjectApprovalService $approval) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->approval->canApprove($user)) {
            $query = ProjectChangeRequest::query()
                ->with(['requester', 'project', 'reviewer'])
                ->orderByDesc('created_at');

            if ($request->status) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', ProjectChangeRequest::STATUS_PENDING);
            }

            $requests = $query->paginate(20)->withQueryString();
            $stats = [
                'pending' => ProjectChangeRequest::where('status', ProjectChangeRequest::STATUS_PENDING)->count(),
                'approved' => ProjectChangeRequest::where('status', ProjectChangeRequest::STATUS_APPROVED)->whereMonth('reviewed_at', now()->month)->count(),
                'rejected' => ProjectChangeRequest::where('status', ProjectChangeRequest::STATUS_REJECTED)->whereMonth('reviewed_at', now()->month)->count(),
            ];

            return view('crm.projects.approvals.index', compact('requests', 'stats'));
        }

        $requests = ProjectChangeRequest::query()
            ->with(['project', 'reviewer'])
            ->where('requested_by', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('crm.projects.my-requests', compact('requests'));
    }

    public function show(ProjectChangeRequest $changeRequest)
    {
        $user = Auth::user();

        if (!$this->approval->canApprove($user) && (int) $changeRequest->requested_by !== (int) $user->id) {
            abort(403);
        }

        $changeRequest->load(['requester', 'project', 'reviewer']);

        return view('crm.projects.approvals.show', [
            'request' => $changeRequest,
            'canApprove' => $this->approval->canApprove($user) && $changeRequest->status === ProjectChangeRequest::STATUS_PENDING,
        ]);
    }

    public function approve(Request $request, ProjectChangeRequest $changeRequest)
    {
        $project = $this->approval->approve($changeRequest, Auth::user(), $request->input('review_notes'));

        if ($project) {
            return redirect()->route('crm.projects.show', $project)
                ->with('success', 'تمت الموافقة وتنفيذ الطلب بنجاح.');
        }

        return redirect()->route('crm.projects.index')
            ->with('success', 'تمت الموافقة على حذف المشروع.');
    }

    public function reject(Request $request, ProjectChangeRequest $changeRequest)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);

        $this->approval->reject($changeRequest, Auth::user(), $request->input('review_notes'));

        return redirect()->route('crm.projects.approvals.index')
            ->with('success', 'تم رفض الطلب.');
    }
}
