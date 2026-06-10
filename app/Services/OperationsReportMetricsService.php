<?php

namespace App\Services;

use App\Models\User;
use App\Services\Operations\OperationsKpiService;
use Carbon\Carbon;

class OperationsReportMetricsService
{
    public function build(User $user, string $periodType, Carbon $start, Carbon $end): array
    {
        $data = app(OperationsKpiService::class)->collect($start, $end, $user);
        $raw = $data['raw'];

        return array_merge($raw, [
            'open_issues' => (int) ($raw['stale_leads'] ?? 0) + (int) ($raw['unassigned_leads'] ?? 0),
            'period_type' => $periodType,
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}
