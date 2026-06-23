<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectApprovalService
{
    public function __construct(
        protected ProjectManagementService $projects,
        protected CrmRecordApprovalService $approval,
    ) {}

    public function requiresApproval(User $user): bool
    {
        return $this->approval->requiresApproval($user);
    }

    public function canApprove(User $user): bool
    {
        return $this->approval->canApproveProjects($user);
    }

    public function submitCreate(Request $request, User $user): ProjectChangeRequest
    {
        $data = $this->projects->validate($request);
        $data = $this->projects->normalize($data, $request, $user, null);
        $data = $this->projects->resolveDeveloper($data, $user);

        $change = ProjectChangeRequest::create([
            'action' => ProjectChangeRequest::ACTION_CREATE,
            'status' => ProjectChangeRequest::STATUS_PENDING,
            'requested_by' => $user->id,
            'summary' => 'طلب إضافة: ' . ($data['name'] ?? 'مشروع جديد'),
            'payload' => [
                'project' => $data,
                'team_members' => $request->input('team_members', []),
                'map_pins' => $request->input('map_pins', []),
            ],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function submitUpdate(Request $request, Project $project, User $user): ProjectChangeRequest
    {
        $this->assertNoPendingConflict($project, ProjectChangeRequest::ACTION_UPDATE);

        $data = $this->projects->validate($request, $project);
        $data = $this->projects->normalize($data, $request, $user, $project);
        $data = $this->projects->resolveDeveloper($data, $user);

        $change = ProjectChangeRequest::create([
            'action' => ProjectChangeRequest::ACTION_UPDATE,
            'status' => ProjectChangeRequest::STATUS_PENDING,
            'project_id' => $project->id,
            'requested_by' => $user->id,
            'summary' => 'طلب تعديل: ' . $project->name,
            'payload' => [
                'project' => $data,
                'team_members' => $request->input('team_members', []),
                'map_pins' => $request->input('map_pins', []),
            ],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function submitDelete(Project $project, User $user, string $reason): ProjectChangeRequest
    {
        if (!$project->isDeletable()) {
            abort(422, 'لا يمكن حذف مشروع مرتبط بصفقات أو يحتوي على وحدات مباعة.');
        }

        $this->assertNoPendingConflict($project, ProjectChangeRequest::ACTION_DELETE);

        $change = ProjectChangeRequest::create([
            'action' => ProjectChangeRequest::ACTION_DELETE,
            'status' => ProjectChangeRequest::STATUS_PENDING,
            'project_id' => $project->id,
            'requested_by' => $user->id,
            'request_reason' => $reason,
            'summary' => 'طلب حذف: ' . $project->name,
            'payload' => [
                'project_name' => $project->name,
                'delete_reason' => $reason,
            ],
        ]);

        $this->notifyApprovers($change->load('requester'));

        return $change;
    }

    public function approve(ProjectChangeRequest $change, User $reviewer, ?string $notes = null): ?Project
    {
        if (!$this->canApprove($reviewer)) {
            abort(403);
        }

        if ((int) $change->requested_by === (int) $reviewer->id) {
            abort(403, 'لا يمكنك الموافقة على طلبك الخاص.');
        }

        if ($change->status !== ProjectChangeRequest::STATUS_PENDING) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً.');
        }

        return DB::transaction(function () use ($change, $reviewer, $notes) {
            $project = match ($change->action) {
                ProjectChangeRequest::ACTION_CREATE => $this->applyCreate($change, $reviewer),
                ProjectChangeRequest::ACTION_UPDATE => $this->applyUpdate($change, $reviewer),
                ProjectChangeRequest::ACTION_DELETE => $this->applyDelete($change, $reviewer),
                default => abort(422, 'نوع طلب غير مدعوم'),
            };

            $change->update([
                'status' => ProjectChangeRequest::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
                'project_id' => $project?->id ?? $change->project_id,
            ]);

            CrmNotificationService::notify(
                $change->requested_by,
                'project_change_approved',
                'تمت الموافقة على طلب المشروع',
                $change->summary . ' — تم التنفيذ.',
                ['request_id' => $change->id, 'project_id' => $project?->id],
            );

            return $project;
        });
    }

    public function reject(ProjectChangeRequest $change, User $reviewer, ?string $notes = null): void
    {
        if (!$this->canApprove($reviewer)) {
            abort(403);
        }

        if ($change->status !== ProjectChangeRequest::STATUS_PENDING) {
            abort(422, 'تمت معالجة هذا الطلب مسبقاً.');
        }

        $change->update([
            'status' => ProjectChangeRequest::STATUS_REJECTED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);

        CrmNotificationService::notify(
            $change->requested_by,
            'project_change_rejected',
            'تم رفض طلب المشروع',
            ($notes ? $notes . ' — ' : '') . $change->summary,
            ['request_id' => $change->id],
        );
    }

    public function pendingForProject(Project $project): ?ProjectChangeRequest
    {
        return ProjectChangeRequest::query()
            ->where('project_id', $project->id)
            ->where('status', ProjectChangeRequest::STATUS_PENDING)
            ->latest()
            ->first();
    }

    protected function applyCreate(ProjectChangeRequest $change, User $reviewer): Project
    {
        $payload = $change->payload ?? [];
        $projectData = $payload['project'] ?? [];
        if (empty($projectData['created_by']) && $change->requested_by) {
            $projectData['created_by'] = $change->requested_by;
        }
        $project = Project::create($projectData);

        if (!empty($payload['team_members'])) {
            $project->teamMembers()->attach($payload['team_members']);
        }

        $fakeRequest = Request::create('/', 'POST', [
            'map_pins' => $payload['map_pins'] ?? [],
            'team_members' => $payload['team_members'] ?? [],
        ]);
        $this->projects->syncMapPins($project, $fakeRequest, $reviewer);

        return $project;
    }

    protected function applyUpdate(ProjectChangeRequest $change, User $reviewer): Project
    {
        $project = Project::findOrFail($change->project_id);
        $payload = $change->payload ?? [];

        $project->update($payload['project'] ?? []);

        if (array_key_exists('team_members', $payload)) {
            $project->teamMembers()->sync($payload['team_members'] ?? []);
        }

        $fakeRequest = Request::create('/', 'POST', [
            'map_pins' => $payload['map_pins'] ?? [],
            'team_members' => $payload['team_members'] ?? [],
        ]);
        $this->projects->syncMapPins($project, $fakeRequest, $reviewer);

        return $project->fresh();
    }

    protected function applyDelete(ProjectChangeRequest $change, User $reviewer): ?Project
    {
        $project = Project::findOrFail($change->project_id);
        $this->projects->deleteProject($project, $reviewer);

        return null;
    }

    protected function assertNoPendingConflict(Project $project, string $action): void
    {
        $exists = ProjectChangeRequest::query()
            ->where('project_id', $project->id)
            ->where('status', ProjectChangeRequest::STATUS_PENDING)
            ->whereIn('action', [$action, ProjectChangeRequest::ACTION_DELETE])
            ->exists();

        if ($exists) {
            abort(422, 'يوجد طلب معلّق على هذا المشروع — انتظر موافقة الإدارة.');
        }
    }

    protected function notifyApprovers(ProjectChangeRequest $change): void
    {
        $approvers = User::role(['super_admin', 'admin'])->get();

        foreach ($approvers as $admin) {
            CrmNotificationService::notify(
                $admin->id,
                'project_change_pending',
                'طلب مشروع بانتظار الموافقة',
                $change->summary . ' — من ' . ($change->requester?->name ?? 'موظف'),
                [
                    'request_id' => $change->id,
                    'url' => route('crm.projects.approvals.show', $change),
                ],
                'project_change_pending:' . $change->id,
            );
        }
    }
}
