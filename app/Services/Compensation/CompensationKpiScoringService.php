<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompKpiTemplate;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\User;

class CompensationKpiScoringService
{
    public function score(CompKpiTemplate $template, array $actuals): array
    {
        $items = [];
        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($template->items as $item) {
            $actual = (float) ($actuals[$item->slug] ?? 0);
            $target = (float) $item->target_value;
            $achievement = $target > 0 ? min(150, round(($actual / $target) * 100, 2)) : ($actual > 0 ? 100 : 0);
            $weight = (float) $item->weight;
            $weightedSum += $achievement * ($weight / 100);
            $totalWeight += $weight;

            $items[] = [
                'slug' => $item->slug,
                'name' => $item->name,
                'weight' => $weight,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $achievement,
                'weighted' => round($achievement * ($weight / 100), 2),
            ];
        }

        $overall = $totalWeight > 0 ? round($weightedSum * (100 / $totalWeight), 2) : 0;

        return [
            'items' => $items,
            'overall_score' => $overall,
            'level' => $this->performanceLevel($overall),
        ];
    }

    public function performanceLevel(float $score): array
    {
        foreach (config('compensation.performance_levels', []) as $level) {
            if ($score >= $level['min'] && $score <= $level['max']) {
                return $level;
            }
        }

        return ['key' => 'unknown', 'label' => '—'];
    }

    public function evaluateUser(User $user, CompPayrollPeriod $period): array
    {
        $profile = $user->compensationProfile;
        $template = $profile?->kpiTemplate;

        if (!$template) {
            return ['items' => [], 'overall_score' => 0, 'level' => ['key' => 'none', 'label' => 'بدون قالب KPI']];
        }

        $actuals = CompensationKpiMetricsService::for($user)->collect(
            $user,
            $period,
            $template->target_role,
        );

        return $this->score($template, $actuals);
    }
}
