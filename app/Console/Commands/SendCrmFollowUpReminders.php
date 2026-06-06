<?php

namespace App\Console\Commands;

use App\Models\CrmFollowUp;
use App\Services\CrmNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCrmFollowUpReminders extends Command
{
    protected $signature = 'crm:follow-up-reminders';

    protected $description = 'إرسال تذكيرات وإشعارات مواعيد متابعات CRM';

    public function handle(): int
    {
        $now = now();

        $upcoming = CrmFollowUp::scheduled()
            ->whereNull('reminder_sent_at')
            ->whereBetween('scheduled_at', [$now, $now->copy()->addMinutes(30)])
            ->with(['client', 'user'])
            ->get();

        foreach ($upcoming->groupBy('user_id') as $userId => $items) {
            $user = $items->first()->user;
            if ($user) {
                CrmNotificationService::notifyFollowUpReminderBatch($user, $items, 'upcoming');
            }
            CrmFollowUp::whereIn('id', $items->pluck('id'))->update(['reminder_sent_at' => $now]);
        }

        $overdue = CrmFollowUp::scheduled()
            ->where('scheduled_at', '<', $now)
            ->whereNull('overdue_notified_at')
            ->with(['client', 'user'])
            ->get();

        foreach ($overdue->groupBy('user_id') as $userId => $items) {
            $user = $items->first()->user;
            if ($user) {
                CrmNotificationService::notifyFollowUpReminderBatch($user, $items, 'overdue');
            }
            CrmFollowUp::whereIn('id', $items->pluck('id'))->update(['overdue_notified_at' => $now]);
        }

        $this->info('Reminder batches: ' . $upcoming->groupBy('user_id')->count() . ', Overdue batches: ' . $overdue->groupBy('user_id')->count());

        return self::SUCCESS;
    }
}
