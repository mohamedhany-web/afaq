<?php

namespace App\Http\Controllers\Crm\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Compensation\CompAdjustment;
use App\Models\Compensation\CompPayrollRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompensationReportController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
            abort(403);
        }

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $runs = CompPayrollRun::with(['user', 'period'])
            ->whereHas('period', fn ($q) => $q->where('year', $year)->where('month', $month))
            ->get();

        return view('crm.compensation.admin.reports.index', [
            'year' => $year,
            'month' => $month,
            'runs' => $runs,
            'bonuses' => CompAdjustment::where('type', 'bonus')->where('status', 'approved')
                ->whereHas('period', fn ($q) => $q->where('year', $year)->where('month', $month))->get(),
            'deductions' => CompAdjustment::where('type', 'deduction')->where('status', 'approved')
                ->whereHas('period', fn ($q) => $q->where('year', $year)->where('month', $month))->get(),
        ]);
    }
}
