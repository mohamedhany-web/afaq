<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Operations\OperationsClientBucketService;
use App\Services\Operations\OperationsKpiService;
use App\Services\Operations\OperationsLeadDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationsClientController extends Controller
{
    public function __construct(
        protected OperationsClientBucketService $buckets,
        protected OperationsLeadDistributionService $distribution,
        protected OperationsKpiService $kpis,
    ) {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $view = $request->get('view', 'data') === 'distribution' ? 'distribution' : 'data';

        if ($view === 'distribution') {
            return $this->distributionView($request);
        }

        return $this->dataView($request);
    }

    protected function dataView(Request $request)
    {
        $bucket = $this->buckets->resolveBucket($request->get('bucket', OperationsClientBucketService::BUCKET_ALL));
        $labels = $this->buckets->labels();

        $query = $this->buckets->applyBucket($this->buckets->baseQuery(), $bucket)
            ->with('assignedEmployee:id,first_name,last_name')
            ->orderByDesc('updated_at');

        if ($employeeId = $request->integer('employee_id')) {
            $query->where('assigned_to', $employeeId);
        }

        $search = trim((string) $request->search);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $clients = $query->paginate(25)->withQueryString();

        $bucketCounts = collect($labels)->mapWithKeys(
            fn ($label, $key) => [$key => $this->buckets->count($key)]
        );

        return view('operations.clients.index', [
            'view' => 'data',
            'clients' => $clients,
            'bucket' => $bucket,
            'bucketLabels' => $labels,
            'bucketCounts' => $bucketCounts,
            'search' => $search,
            'unassignedCount' => $this->distribution->unassignedLeadsQuery()->count(),
        ]);
    }

    protected function distributionView(Request $request)
    {
        $filter = $request->get('filter', 'unassigned');

        $baseQuery = match ($filter) {
            'stale' => Client::query()
                ->whereNull('assigned_to')
                ->where('updated_at', '<', now()->subDays(3))
                ->orderByDesc('updated_at'),
            default => $this->distribution->unassignedLeadsQuery(),
        };

        $search = trim((string) $request->search);
        $leads = (clone $baseQuery)
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $s = '%' . $search . '%';
                $q->where('name', 'like', $s)->orWhere('phone', 'like', $s);
            }))
            ->paginate(20)
            ->withQueryString();

        $kpiData = $this->kpis->collect();

        return view('operations.clients.index', [
            'view' => 'distribution',
            'leads' => $leads,
            'filter' => $filter,
            'search' => $search,
            'reps' => $this->distribution->assignableReps(Auth::user()),
            'repLoads' => $this->distribution->repLoads(Auth::user()),
            'leadKpis' => $kpiData['groups']['lead_management'] ?? null,
            'stats' => [
                'unassigned' => $this->distribution->unassignedLeadsQuery()->count(),
                'stale' => Client::query()
                    ->whereNull('assigned_to')
                    ->where('updated_at', '<', now()->subDays(3))
                    ->count(),
            ],
            'unassignedCount' => $this->distribution->unassignedLeadsQuery()->count(),
        ]);
    }
}
