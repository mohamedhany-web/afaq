<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentReport;
use App\Models\Project;
use Illuminate\Http\Request;

class DepartmentOversightController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->can('view-reports') || $request->user()?->can('view-departments'), 403);

        $departments = Department::query()
            ->with(['manager.user'])
            ->withCount(['employees', 'projects'])
            ->orderBy('name')
            ->get();

        $deptIds = $departments->pluck('id');

        $latestReportByDept = DepartmentReport::query()
            ->selectRaw('department_id, max(created_at) as latest_created_at')
            ->whereIn('department_id', $deptIds)
            ->groupBy('department_id')
            ->pluck('latest_created_at', 'department_id');

        $recentReports = DepartmentReport::query()
            ->with(['department.manager.user', 'project', 'creator'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'departments_total' => $departments->count(),
            'projects_total' => Project::whereIn('department_id', $deptIds)->count(),
            'reports_total' => DepartmentReport::count(),
        ];

        return view('admin.department-oversight.index', compact(
            'departments',
            'latestReportByDept',
            'recentReports',
            'stats'
        ));
    }
}
