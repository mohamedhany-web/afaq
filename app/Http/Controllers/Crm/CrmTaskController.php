<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmTask;
use App\Models\Project;
use App\Models\User;
use App\Services\CrmScopeService;
use App\Services\Tasks\CrmTaskDashboardService;
use App\Services\Tasks\CrmTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmTaskController extends Controller
{
    public function index(Request $request, CrmTaskService $tasks, CrmTaskDashboardService $dashboard)
    {
        $user = Auth::user();
        $query = $tasks->tasksQuery($user);

        if ($filter = $request->get('filter')) {
            match ($filter) {
                'today' => $query->active()->dueToday(),
                'overdue' => $query->overdue(),
                'critical' => $query->active()->where('priority', 'critical'),
                'completed' => $query->whereIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED]),
                'high' => $query->active()->whereIn('priority', ['high', 'critical']),
                default => $query->active(),
            };
        } else {
            $query->active();
        }

        if ($request->filled('assignee') && $tasks->canAssignTo($user, (int) $request->assignee)) {
            $query->where('assigned_to', $request->assignee);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $taskList = $query->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->orderBy('due_at')
            ->paginate(20)
            ->withQueryString();

        return view('crm.tasks.index', [
            'tasks' => $taskList,
            'stats' => $dashboard->stats($user),
            'teamProductivity' => $dashboard->teamProductivity($user),
            'adminOverview' => $dashboard->adminOverview($user),
            'viewMode' => $dashboard->viewMode($user),
            'canCreate' => $tasks->assignableUsers($user)->isNotEmpty(),
            'assignableUsers' => $tasks->assignableUsers($user),
            'filter' => $filter ?? 'active',
        ]);
    }

    public function create(CrmTaskService $tasks)
    {
        $user = Auth::user();
        $assignable = $tasks->assignableUsers($user);

        if ($assignable->isEmpty()) {
            abort(403, 'لا يمكنك إنشاء مهام.');
        }

        return view('crm.tasks.create', $this->formData($user, $assignable));
    }

    public function store(Request $request, CrmTaskService $tasks)
    {
        $data = $this->validated($request);
        $task = $tasks->create(Auth::user(), $data);

        return redirect()->route('crm.tasks.show', $task)->with('success', 'تم إنشاء المهمة وتعيينها');
    }

    public function show(CrmTask $task, CrmTaskService $tasks)
    {
        if (!$tasks->canView(Auth::user(), $task)) {
            abort(403);
        }

        $task->load(['assignee', 'assigner', 'client', 'sale.client', 'project', 'logs.user']);

        return view('crm.tasks.show', [
            'task' => $task,
            'canManage' => $tasks->assignableUsers(Auth::user())->isNotEmpty(),
            'isAssignee' => (int) $task->assigned_to === (int) Auth::id(),
            'canVerify' => CrmScopeService::for(Auth::user())->isManagerScope() || CrmScopeService::for(Auth::user())->hasFullAccess(),
        ]);
    }

    public function edit(CrmTask $task, CrmTaskService $tasks)
    {
        if (!$tasks->canView(Auth::user(), $task) || $tasks->assignableUsers(Auth::user())->isEmpty()) {
            abort(403);
        }

        return view('crm.tasks.edit', array_merge(
            ['task' => $task],
            $this->formData(Auth::user(), $tasks->assignableUsers(Auth::user())),
        ));
    }

    public function update(Request $request, CrmTask $task, CrmTaskService $tasks)
    {
        $data = $this->validated($request);
        $tasks->update(Auth::user(), $task, $data);

        return redirect()->route('crm.tasks.show', $task)->with('success', 'تم تحديث المهمة');
    }

    public function accept(CrmTask $task, CrmTaskService $tasks)
    {
        $tasks->accept(Auth::user(), $task);

        return back()->with('success', 'تم قبول المهمة');
    }

    public function start(CrmTask $task, CrmTaskService $tasks)
    {
        $tasks->start(Auth::user(), $task);

        return back()->with('success', 'المهمة قيد التنفيذ');
    }

    public function complete(Request $request, CrmTask $task, CrmTaskService $tasks)
    {
        $request->validate(['completion_notes' => 'required|string|min:10|max:5000']);
        $tasks->complete(Auth::user(), $task, $request->completion_notes);

        return redirect()->route('crm.tasks.show', $task)->with('success', 'تم إكمال المهمة');
    }

    public function verify(CrmTask $task, CrmTaskService $tasks)
    {
        $tasks->verify(Auth::user(), $task);

        return back()->with('success', 'تم التحقق من المهمة');
    }

    public function cancel(Request $request, CrmTask $task, CrmTaskService $tasks)
    {
        $tasks->cancel(Auth::user(), $task, $request->reason);

        return redirect()->route('crm.tasks.index')->with('success', 'تم إلغاء المهمة');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:' . implode(',', config('crm_tasks.priorities', [])),
            'category' => 'required|in:' . implode(',', config('crm_tasks.categories', [])),
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'sale_id' => 'nullable|exists:sales,id',
            'due_at' => 'required|date|after:now',
            'requires_acceptance' => 'nullable|boolean',
        ], [
            'due_at.after' => 'موعد الاستحقاق يجب أن يكون في المستقبل.',
            'title.required' => 'عنوان المهمة مطلوب.',
        ]);
    }

    protected function formData(User $user, $assignable): array
    {
        $scope = CrmScopeService::for($user);
        $clients = $scope->clientsQuery()->orderBy('name')->limit(200)->get(['id', 'name', 'phone']);
        $projects = Project::query()->orderBy('name')->limit(100)->get(['id', 'name']);
        $sales = $scope->salesQuery()->with('client:id,name')->limit(100)->get(['id', 'client_id', 'stage']);

        return [
            'assignableUsers' => $assignable,
            'clients' => $clients,
            'projects' => $projects,
            'sales' => $sales,
            'priorities' => config('crm_tasks.priority_labels', []),
            'categories' => config('crm_tasks.category_labels', []),
        ];
    }
}
