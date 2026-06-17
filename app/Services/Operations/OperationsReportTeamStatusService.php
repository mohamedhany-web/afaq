<?php

namespace App\Services\Operations;

use App\Models\DailySalesReport;
use App\Models\OperationsPeriodReport;
use App\Models\User;
use App\Services\CrmEmployeeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OperationsReportTeamStatusService
{
    /** @return Collection<int, array{user: User, submitted: bool, reports_count: int, notes: ?string, report_url: ?string}> */
    public function salesRepsForPeriod(string $periodType, ?Carbon $anchor = null): Collection
    {
        $anchor ??= now()->startOfDay();
        $period = OperationsPeriodReport::resolvePeriod($periodType, $anchor);
        $start = $period['start']->copy()->startOfDay();
        $end = $period['end']->copy()->endOfDay();

        $reps = User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
            ->whereHas('employee', fn ($q) => $q
                ->where('department_id', CrmEmployeeService::salesDepartment()->id)
                ->where('status', 'active'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $reportsByUser = DailySalesReport::query()
            ->whereIn('user_id', $reps->pluck('id'))
            ->where('status', DailySalesReport::STATUS_SUBMITTED)
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('report_date')
            ->get()
            ->groupBy('user_id');

        return $reps->map(function (User $rep) use ($periodType, $start, $reportsByUser) {
            $userReports = $reportsByUser->get($rep->id, collect());
            $latest = $userReports->first();

            $submitted = match ($periodType) {
                OperationsPeriodReport::PERIOD_DAILY => $userReports->contains(
                    fn (DailySalesReport $r) => $r->report_date->toDateString() === $start->toDateString()
                ),
                default => $userReports->isNotEmpty(),
            };

            $notes = $this->composeRepNotes($latest);

            return [
                'user' => $rep,
                'submitted' => $submitted,
                'reports_count' => $userReports->count(),
                'notes' => $notes,
                'report_url' => $latest ? route('crm.daily-reports.show', $latest) : null,
            ];
        });
    }

    protected function composeRepNotes(?DailySalesReport $report): ?string
    {
        if (!$report) {
            return null;
        }

        $parts = array_filter([
            $report->obstacles ? 'عقبات: ' . $report->obstacles : null,
            $report->support_required ? 'دعم: ' . $report->support_required : null,
        ]);

        return $parts !== [] ? implode(' | ', $parts) : null;
    }
}
