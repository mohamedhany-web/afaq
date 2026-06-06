<?php

namespace App\Services\Tasks;

use App\Models\CrmTask;

class CrmTaskScoringService
{
    /**
     * Score 0–100 based on timeliness and priority weight.
     */
    public function score(CrmTask $task): float
    {
        if (!$task->completed_at || !$task->due_at) {
            return 0;
        }

        $due = $task->due_at;
        $done = $task->completed_at;
        $hoursEarly = $due->diffInHours($done, false);

        $timeliness = match (true) {
            $hoursEarly >= 24 => 100,
            $hoursEarly >= 0 => 85,
            $hoursEarly >= -4 => 70,
            $hoursEarly >= -24 => 50,
            default => 30,
        };

        $priorityBoost = match ($task->priority) {
            'critical' => 10,
            'high' => 5,
            default => 0,
        };

        return min(100, round($timeliness + $priorityBoost, 2));
    }
}
