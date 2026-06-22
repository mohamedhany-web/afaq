<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CrmTask;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\Operations\OperationsClientBucketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsRepController extends Controller
{
    public function __construct(
        protected OperationsClientBucketService $buckets,
    ) {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function search(Request $request)
    {
        if ($request->filled('rep_id')) {
            $rep = User::findOrFail($request->integer('rep_id'));
            $this->ensureManagedRep($rep);

            return redirect()->route('operations.reps.show', $rep);
        }

        $q = trim((string) $request->get('q', ''));
        $salesReps = CrmEmployeeService::searchableSalesUsersQuery()->get();

        $reps = $this->managedRepsQuery()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderBy('name')
            ->get();

        return view('operations.reps.search', [
            'reps' => $reps,
            'salesReps' => $salesReps,
            'q' => $q,
            'selectedRepId' => $request->integer('rep_id') ?: null,
        ]);
    }

    public function show(User $rep)
    {
        $this->ensureManagedRep($rep);

        $employeeId = $rep->employee?->id;

        $clientStats = [
            'all' => Client::query()->when($employeeId, fn ($q) => $q->where('assigned_to', $employeeId))->count(),
            'interested' => $this->buckets->applyBucket(
                Client::query()->when($employeeId, fn ($q) => $q->where('assigned_to', $employeeId)),
                OperationsClientBucketService::BUCKET_INTERESTED
            )->count(),
            'follow_up' => $this->buckets->applyBucket(
                Client::query()->when($employeeId, fn ($q) => $q->where('assigned_to', $employeeId)),
                OperationsClientBucketService::BUCKET_FOLLOW_UP
            )->count(),
        ];

        $tasks = CrmTask::query()
            ->where('assigned_to', $rep->id)
            ->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_VERIFIED, CrmTask::STATUS_CANCELLED, CrmTask::STATUS_ARCHIVED])
            ->orderBy('due_at')
            ->limit(10)
            ->get();

        $recentClients = Client::query()
            ->when($employeeId, fn ($q) => $q->where('assigned_to', $employeeId))
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('operations.reps.show', [
            'rep' => $rep->load('employee.department'),
            'clientStats' => $clientStats,
            'tasks' => $tasks,
            'recentClients' => $recentClients,
            'employeeId' => $employeeId,
        ]);
    }

    protected function managedRepsQuery()
    {
        return CrmEmployeeService::searchableSalesUsersQuery();
    }

    protected function ensureManagedRep(User $rep): void
    {
        if (!$this->managedRepsQuery()->where('users.id', $rep->id)->exists()) {
            abort(404);
        }
    }
}
