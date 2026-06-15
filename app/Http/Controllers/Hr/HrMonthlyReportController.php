<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Services\Hr\HrMonthlyAttendanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrMonthlyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessHr()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request, HrMonthlyAttendanceReportService $reports)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month . '-01')->startOfMonth()
            : Carbon::now()->startOfMonth();

        $data = $reports->build(
            $month,
            $request->integer('department_id') ?: null,
            $request->integer('employee_id') ?: null,
        );

        return view('hr.reports.monthly', array_merge($data, [
            'filters' => [
                'month' => $month->format('Y-m'),
                'department_id' => $request->department_id,
                'employee_id' => $request->employee_id,
            ],
        ]));
    }

    public function print(Request $request, HrMonthlyAttendanceReportService $reports)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month . '-01')->startOfMonth()
            : Carbon::now()->startOfMonth();

        $data = $reports->build(
            $month,
            $request->integer('department_id') ?: null,
            $request->integer('employee_id') ?: null,
        );

        return view('hr.reports.monthly-print', $data);
    }
}
