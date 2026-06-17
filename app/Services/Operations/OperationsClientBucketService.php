<?php

namespace App\Services\Operations;

use App\Models\Client;
use App\Models\CrmFollowUp;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;

class OperationsClientBucketService
{
    public const BUCKET_ALL = 'all';
    public const BUCKET_FOLLOW_UP = 'follow_up';
    public const BUCKET_INTERESTED = 'interested';
    public const BUCKET_CANCELLED = 'cancelled';
    public const BUCKET_NOT_INTERESTED = 'not_interested';
    public const BUCKET_CONTRACTS = 'contracts';

    /** @return array<string, string> */
    public function labels(): array
    {
        return [
            self::BUCKET_ALL => __('operations.buckets.all'),
            self::BUCKET_FOLLOW_UP => __('operations.buckets.follow_up'),
            self::BUCKET_INTERESTED => __('operations.buckets.interested'),
            self::BUCKET_CANCELLED => __('operations.buckets.cancelled'),
            self::BUCKET_NOT_INTERESTED => __('operations.buckets.not_interested'),
            self::BUCKET_CONTRACTS => __('operations.buckets.contracts'),
        ];
    }

    public function baseQuery(): Builder
    {
        return Client::query();
    }

    public function applyBucket(Builder $query, string $bucket): Builder
    {
        return match ($bucket) {
            self::BUCKET_ALL => $query,
            self::BUCKET_FOLLOW_UP => $this->applyFollowUp($query),
            self::BUCKET_INTERESTED => $this->applyInterested($query),
            self::BUCKET_CANCELLED => $this->applyCancelled($query),
            self::BUCKET_NOT_INTERESTED => $this->applyNotInterested($query),
            self::BUCKET_CONTRACTS => $query->where(function (Builder $q) {
                $q->where('lead_stage', 'closed_won')
                    ->orWhereHas('sales', fn (Builder $s) => $s->where('stage', 'closed_won'));
            }),
            default => $query,
        };
    }

    public function count(string $bucket): int
    {
        return $this->applyBucket($this->baseQuery(), $bucket)->count();
    }

    /** نفس منطق عدّاد العملاء المحتملين في لوحة العمليات */
    public function applyInterested(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', 'prospect')
                ->orWhereIn('lead_stage', ['lead', 'prospect', 'proposal']);
        })->whereNotIn('lead_stage', ['closed_won', 'closed_lost']);
    }

    protected function applyFollowUp(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereIn('lead_stage', ['negotiation'])
                ->orWhereHas('followUps', fn (Builder $f) => $f->where('status', CrmFollowUp::STATUS_SCHEDULED));
        })->whereNotIn('lead_stage', ['closed_won', 'closed_lost']);
    }

    protected function applyCancelled(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('lead_stage', 'closed_lost')
                ->orWhereHas('sales', fn (Builder $s) => $s->where('stage', 'closed_lost'));
        });
    }

    protected function applyNotInterested(Builder $query): Builder
    {
        return $query->where('lead_stage', 'closed_lost')
            ->whereNotNull('lost_reason');
    }

    public function resolveBucket(?string $bucket): string
    {
        $bucket = $bucket ?: self::BUCKET_ALL;

        return array_key_exists($bucket, $this->labels()) ? $bucket : self::BUCKET_ALL;
    }

    public function contractsCount(): int
    {
        return Sale::query()->where('stage', 'closed_won')->count();
    }
}
