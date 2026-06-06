<?php

namespace App\Services;

use App\Models\MarketingActivity;
use Carbon\Carbon;

class MarketingRecurrenceService
{
    public function processDueRecurrences(): int
    {
        $count = 0;

        MarketingActivity::query()
            ->where('recurrence', '!=', 'none')
            ->where('status', MarketingActivity::STATUS_COMPLETED)
            ->whereNotNull('next_occurrence_at')
            ->where('next_occurrence_at', '<=', now())
            ->each(function (MarketingActivity $activity) use (&$count) {
                $this->spawnNextOccurrence($activity);
                $count++;
            });

        return $count;
    }

    public function scheduleNextOnComplete(MarketingActivity $activity): void
    {
        if ($activity->recurrence === 'none') {
            return;
        }

        $next = $this->calculateNextDue($activity->due_at ?? now(), $activity->recurrence, $activity->recurrence_interval);

        $activity->update(['next_occurrence_at' => $next]);
    }

    protected function spawnNextOccurrence(MarketingActivity $template): void
    {
        $dueAt = $this->calculateNextDue(
            $template->next_occurrence_at ?? now(),
            $template->recurrence,
            $template->recurrence_interval
        );

        MarketingActivity::create([
            'title' => $template->title,
            'description' => $template->description,
            'type' => $template->type,
            'status' => MarketingActivity::STATUS_PENDING,
            'priority' => $template->priority,
            'campaign_id' => $template->campaign_id,
            'assigned_to' => $template->assigned_to,
            'assigned_by' => $template->assigned_by,
            'due_at' => $dueAt,
            'recurrence' => $template->recurrence,
            'recurrence_interval' => $template->recurrence_interval,
            'parent_activity_id' => $template->parent_activity_id ?? $template->id,
            'notes' => $template->notes,
        ]);

        $template->update([
            'next_occurrence_at' => $this->calculateNextDue($dueAt, $template->recurrence, $template->recurrence_interval),
        ]);
    }

    protected function calculateNextDue(Carbon $from, string $recurrence, int $interval): Carbon
    {
        $base = $from->copy();

        return match ($recurrence) {
            'daily' => $base->addDays($interval),
            'weekly' => $base->addWeeks($interval),
            'monthly' => $base->addMonths($interval),
            default => $base->addDay(),
        };
    }
}
