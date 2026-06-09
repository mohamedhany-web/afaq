<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        // إحصائيات عامة
        $stats = [
            'total_employees' => Employee::where('status', 'active')->count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('listing_status', ['upcoming', 'active'])->count(),
            'completed_projects' => Project::whereIn('listing_status', ['completed', 'sold_out'])->count(),
            'total_clients' => Client::count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'total_sales' => Sale::sum('estimated_value'),
        ];
        
        return view('reports.index', compact('stats'));
    }

    /**
     * Employee report.
     */
    public function employees(Request $request)
    {
        $department_id = $request->get('department_id');
        $status = $request->get('status');
        
        $query = Employee::with(['department', 'user']);
        
        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $employees = $query->get();
        $departments = Department::where('is_active', true)->get();
        
        $summary = [
            'total' => $employees->count(),
            'total_salary' => $employees->sum('salary'),
            'average_salary' => $employees->avg('salary'),
            'by_department' => $employees->groupBy('department_id')->map(function($group) {
                return $group->count();
            }),
            'by_status' => $employees->groupBy('status')->map(function($group) {
                return $group->count();
            }),
        ];
        
        return view('reports.employees', compact('employees', 'departments', 'summary'));
    }

    /**
     * Employee report - Print version.
     */
    public function employeesPrint(Request $request)
    {
        $department_id = $request->get('department_id');
        $status = $request->get('status');
        
        $query = Employee::with(['department', 'user']);
        
        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $employees = $query->get();
        $departments = Department::where('is_active', true)->get();
        
        $summary = [
            'total' => $employees->count(),
            'total_salary' => $employees->sum('salary'),
            'average_salary' => $employees->avg('salary'),
            'by_department' => $employees->groupBy('department_id')->map(function($group) {
                return $group->count();
            }),
            'by_status' => $employees->groupBy('status')->map(function($group) {
                return $group->count();
            }),
        ];
        
        return view('reports.employees-print', compact('employees', 'departments', 'summary'));
    }

    /**
     * Attendance report.
     */
    public function attendance(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $employee_id = $request->get('employee_id');
        
        $query = Attendance::with('employee')
            ->whereBetween('date', [$start_date, $end_date]);
        
        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }
        
        $attendances = $query->orderBy('date', 'desc')->get();
        $employees = Employee::where('status', 'active')->get();
        
        $summary = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'total_hours' => $attendances->sum('total_hours'),
            'attendance_rate' => $attendances->count() > 0
                ? round(($attendances->whereIn('status', ['present', 'late'])->count() / $attendances->count()) * 100, 2)
                : 0,
        ];

        return view('reports.attendance', compact('attendances', 'employees', 'summary', 'start_date', 'end_date'));
    }

    /**
     * Attendance report - Print version.
     */
    public function attendancePrint(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $employee_id = $request->get('employee_id');
        
        $query = Attendance::with('employee')
            ->whereBetween('date', [$start_date, $end_date]);
        
        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }
        
        $attendances = $query->orderBy('date', 'desc')->get();
        
        $summary = [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'total_hours' => $attendances->sum('total_hours'),
            'attendance_rate' => $attendances->count() > 0 
                ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2)
                : 0,
        ];
        
        return view('reports.attendance-print', compact('attendances', 'summary', 'start_date', 'end_date'));
    }

    /**
     * Sales report.
     */
    public function sales(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $sales = Sale::with('client')
            ->whereBetween('expected_close_date', [$start_date, $end_date])
            ->orderBy('expected_close_date', 'desc')
            ->get();
        
        $summary = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('estimated_value'),
            'average_sale' => $sales->avg('estimated_value'),
            'by_status' => $sales->groupBy('stage')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('estimated_value'),
                ];
            }),
        ];
        
        return view('reports.sales', compact('sales', 'summary', 'start_date', 'end_date'));
    }

    /**
     * Sales report - Print version.
     */
    public function salesPrint(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $sales = Sale::with('client')
            ->whereBetween('expected_close_date', [$start_date, $end_date])
            ->orderBy('expected_close_date', 'desc')
            ->get();
        
        $summary = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('estimated_value'),
            'average_sale' => $sales->avg('estimated_value'),
            'by_status' => $sales->groupBy('stage')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('estimated_value'),
                ];
            }),
        ];
        
        return view('reports.sales-print', compact('sales', 'summary', 'start_date', 'end_date'));
    }

    /**
     * Salaries report.
     */
    public function salaries(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        $department_id = $request->get('department_id');
        
        $query = Salary::with(['employee.department']);
        
        if ($year) {
            $query->whereYear('payment_date', $year);
        }
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }
        
        if ($department_id) {
            $query->whereHas('employee', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }
        
        $salaries = $query->orderBy('payment_date', 'desc')->get();
        $departments = Department::where('is_active', true)->get();
        
        $summary = [
            'total_salaries' => $salaries->count(),
            'total_amount' => $salaries->sum('net_salary'),
            'total_basic' => $salaries->sum('base_salary'),
            'total_bonuses' => $salaries->sum('bonus'),
            'total_deductions' => $salaries->sum('deductions'),
            'by_status' => $salaries->groupBy('status')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('net_salary'),
                ];
            }),
        ];
        
        return view('reports.salaries', compact('salaries', 'departments', 'summary', 'year', 'month'));
    }

    /**
     * Salaries report - Print version.
     */
    public function salariesPrint(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');
        $department_id = $request->get('department_id');
        
        $query = Salary::with(['employee.department']);
        
        if ($year) {
            $query->whereYear('payment_date', $year);
        }
        
        if ($month) {
            $query->whereMonth('payment_date', $month);
        }
        
        if ($department_id) {
            $query->whereHas('employee', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }
        
        $salaries = $query->orderBy('payment_date', 'desc')->get();
        
        $summary = [
            'total_salaries' => $salaries->count(),
            'total_amount' => $salaries->sum('net_salary'),
            'total_basic' => $salaries->sum('base_salary'),
            'total_bonuses' => $salaries->sum('bonus'),
            'total_deductions' => $salaries->sum('deductions'),
            'by_status' => $salaries->groupBy('status')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('net_salary'),
                ];
            }),
        ];
        
        return view('reports.salaries-print', compact('salaries', 'summary', 'year', 'month'));
    }

    /**
     * Departments report.
     */
    public function departments()
    {
        $departments = Department::withCount(['employees', 'projects'])
            ->with('manager')
            ->get();
        
        $summary = [
            'total_departments' => $departments->count(),
            'total_employees' => $departments->sum('employees_count'),
            'total_projects' => $departments->sum('projects_count'),
            'average_employees' => round($departments->avg('employees_count'), 2),
        ];
        
        return view('reports.departments', compact('departments', 'summary'));
    }

    /**
     * Departments report - Print version.
     */
    public function departmentsPrint()
    {
        $departments = Department::withCount(['employees', 'projects'])
            ->with('manager')
            ->get();
        
        $summary = [
            'total_departments' => $departments->count(),
            'total_employees' => $departments->sum('employees_count'),
            'total_projects' => $departments->sum('projects_count'),
            'average_employees' => round($departments->avg('employees_count'), 2),
        ];
        
        return view('reports.departments-print', compact('departments', 'summary'));
    }

    /**
     * Performance report.
     */
    public function performance(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Employee performance metrics
        $employeeMetrics = Employee::where('status', 'active')
            ->withCount([
                'attendances' => function($query) use ($start_date, $end_date) {
                    $query->whereBetween('date', [$start_date, $end_date]);
                }
            ])
            ->get()
            ->map(function($employee) {
                return [
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'department' => $employee->department->name ?? 'N/A',
                    'attendance_rate' => 100, // Simplified
                    'tasks_completed' => 0, // Would need tasks table
                ];
            });
        
        return view('reports.performance', compact('employeeMetrics', 'start_date', 'end_date'));
    }
}
