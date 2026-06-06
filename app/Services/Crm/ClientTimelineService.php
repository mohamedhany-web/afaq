<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\ClientTimelineEvent;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Collection;

class ClientTimelineService
{
    public function record(
        Client $client,
        string $eventType,
        string $title,
        ?string $description = null,
        ?User $user = null,
        string $department = 'sales',
        ?string $relatedType = null,
        ?int $relatedId = null,
        array $meta = [],
        ?\DateTimeInterface $occurredAt = null,
    ): ClientTimelineEvent {
        return ClientTimelineEvent::create([
            'client_id' => $client->id,
            'user_id' => $user?->id,
            'department' => $department,
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'meta' => $meta ?: null,
            'occurred_at' => $occurredAt ?? now(),
        ]);
    }

    public function recordLeadCreated(Client $client, ?User $user = null): ClientTimelineEvent
    {
        return $this->record(
            $client,
            'lead_created',
            'تسجيل عميل جديد',
            $client->name,
            $user ?? $client->createdBy,
            'marketing',
            Client::class,
            $client->id,
            ['lead_stage' => $client->lead_stage ?? 'lead'],
            $client->created_at,
        );
    }

    public function recordStageChange(
        Client $client,
        string $from,
        string $to,
        ?User $user = null,
        ?string $lostReason = null,
        ?string $lostNotes = null,
    ): ClientTimelineEvent {
        $labels = config('crm_intelligence.timeline_event_types');
        $stageLabels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        $meta = ['from' => $from, 'to' => $to];
        $eventType = $to === 'closed_lost' ? 'deal_lost' : 'stage_changed';

        if ($lostReason) {
            $meta['lost_reason'] = $lostReason;
            $meta['lost_reason_label'] = config('crm_intelligence.lost_reasons')[$lostReason] ?? $lostReason;
            $meta['lost_reason_notes'] = $lostNotes;
        }

        return $this->record(
            $client,
            $eventType,
            'تغيير مرحلة: ' . ($stageLabels[$from] ?? $from) . ' ← ' . ($stageLabels[$to] ?? $to),
            $lostNotes,
            $user,
            'sales',
            Client::class,
            $client->id,
            $meta,
        );
    }

    public function recordDealStageChange(
        Sale $sale,
        string $from,
        string $to,
        ?User $user = null,
        ?string $lostReason = null,
        ?string $lostNotes = null,
    ): ClientTimelineEvent {
        $stageLabels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        $eventType = match ($to) {
            'closed_won' => 'deal_won',
            'closed_lost' => 'deal_lost',
            default => 'deal_stage_changed',
        };

        $meta = [
            'from' => $from,
            'to' => $to,
            'sale_id' => $sale->id,
            'product' => $sale->product_service,
            'value' => $sale->estimated_value,
        ];

        if ($lostReason) {
            $meta['lost_reason'] = $lostReason;
            $meta['lost_reason_label'] = config('crm_intelligence.lost_reasons')[$lostReason] ?? $lostReason;
            $meta['lost_reason_notes'] = $lostNotes;
        }

        return $this->record(
            $sale->client,
            $eventType,
            'صفقة: ' . ($stageLabels[$from] ?? $from) . ' ← ' . ($stageLabels[$to] ?? $to),
            $sale->product_service . ($lostNotes ? ' — ' . $lostNotes : ''),
            $user,
            'sales',
            Sale::class,
            $sale->id,
            $meta,
        );
    }

    public function recordInteraction(CrmFollowUp $followUp, ?User $user = null): ClientTimelineEvent
    {
        return $this->record(
            $followUp->client,
            'interaction',
            $followUp->typeLabel(),
            $followUp->notes,
            $user ?? $followUp->creator,
            'sales',
            CrmFollowUp::class,
            $followUp->id,
            [
                'interaction_type' => $followUp->interaction_type,
                'status' => $followUp->status,
                'sale_id' => $followUp->sale_id,
            ],
            $followUp->completed_at ?? $followUp->scheduled_at ?? $followUp->created_at,
        );
    }

    public function buildForClient(Client $client, int $limit = 50): Collection
    {
        $events = ClientTimelineEvent::with('user:id,name')
            ->where('client_id', $client->id)
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();

        if ($events->isNotEmpty()) {
            return $events;
        }

        return $this->synthesizeFromLegacy($client, $limit);
    }

    protected function synthesizeFromLegacy(Client $client, int $limit): Collection
    {
        $items = collect();

        $items->push([
            'occurred_at' => $client->created_at,
            'department' => 'marketing',
            'event_type' => 'lead_created',
            'title' => 'تسجيل عميل جديد',
            'description' => $client->name,
            'user' => $client->createdBy,
            'meta' => ['synthesized' => true],
        ]);

        $client->loadMissing(['sales' => fn ($q) => $q->orderBy('created_at')]);

        foreach ($client->sales as $sale) {
            $items->push([
                'occurred_at' => $sale->created_at,
                'department' => 'sales',
                'event_type' => 'deal_created',
                'title' => 'إنشاء صفقة',
                'description' => $sale->product_service,
                'user' => $sale->salesRep,
                'meta' => ['sale_id' => $sale->id, 'synthesized' => true],
            ]);

            if (in_array($sale->stage, ['closed_won', 'closed_lost'], true)) {
                $items->push([
                    'occurred_at' => $sale->updated_at,
                    'department' => 'sales',
                    'event_type' => $sale->stage === 'closed_won' ? 'deal_won' : 'deal_lost',
                    'title' => $sale->stage === 'closed_won' ? 'إغلاق صفقة — بيع' : 'إغلاق صفقة — خسارة',
                    'description' => $sale->product_service,
                    'user' => $sale->salesRep,
                    'meta' => [
                        'sale_id' => $sale->id,
                        'lost_reason' => $sale->lost_reason,
                        'synthesized' => true,
                    ],
                ]);
            }
        }

        $followUps = CrmFollowUp::with('creator:id,name')
            ->where('client_id', $client->id)
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        foreach ($followUps as $fu) {
            $items->push([
                'occurred_at' => $fu->completed_at ?? $fu->scheduled_at ?? $fu->created_at,
                'department' => 'sales',
                'event_type' => 'interaction',
                'title' => $fu->typeLabel(),
                'description' => $fu->notes,
                'user' => $fu->creator,
                'meta' => ['follow_up_id' => $fu->id, 'synthesized' => true],
            ]);
        }

        return $items
            ->sortByDesc(fn ($e) => $e['occurred_at'])
            ->take($limit)
            ->values();
    }
}
