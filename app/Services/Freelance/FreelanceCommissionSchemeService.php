<?php

namespace App\Services\Freelance;

use App\Models\FreelanceAgentContract;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

class FreelanceCommissionSchemeService
{
    /** @return array<int, array{user_id:int, agent_role:string, percent:float, amount:float}> */
    public function buildSplits(Sale $sale): array
    {
        $base = (float) ($sale->company_commission_amount ?? 0);
        if ($base <= 0 || $sale->stage !== 'closed_won') {
            return [];
        }

        $type = $this->resolveType($sale);
        $scheme = config('freelance_agents.scheme', []);

        return match ($type) {
            'primary' => $this->primarySplits($sale, $base, $scheme),
            'resale_full' => $this->singleAgentSplit($sale->assigned_to, 'selling_agent', $scheme['resale_full']['agent'] ?? 50, $base),
            'resale_listing_only' => $this->singleAgentSplit($sale->listing_agent_id ?? $sale->assigned_to, 'listing_agent', $scheme['resale_listing_only']['listing_agent'] ?? 15, $base),
            'resale_selling_only' => $this->resaleSellingSplits($sale, $base, $scheme),
            'resale_dual' => $this->dualAgentSplits($sale, $base, $scheme),
            'rental' => $this->singleAgentSplit($sale->assigned_to, 'primary_agent', $scheme['rental']['agent'] ?? 50, $base),
            default => [],
        };
    }

    public function resolveType(Sale $sale): string
    {
        if ($sale->transaction_type) {
            return $sale->transaction_type;
        }

        if ($sale->listing_agent_id && $sale->listing_agent_id !== (int) $sale->assigned_to) {
            return 'resale_dual';
        }

        return 'primary';
    }

    /** @return array<int, array{user_id:int, agent_role:string, percent:float, amount:float}> */
    protected function primarySplits(Sale $sale, float $base, array $scheme): array
    {
        $agentId = (int) $sale->assigned_to;
        $metTarget = $this->agentMetQuarterlyTarget($agentId, $sale->actual_close_date);
        $percent = $metTarget
            ? (float) ($scheme['primary_target']['agent'] ?? 50)
            : (float) ($scheme['primary_normal']['agent'] ?? 40);

        return $this->singleAgentSplit($agentId, 'primary_agent', $percent, $base);
    }

    /** @return array<int, array{user_id:int, agent_role:string, percent:float, amount:float}> */
    protected function resaleSellingSplits(Sale $sale, float $base, array $scheme): array
    {
        $sellingPercent = (float) ($scheme['resale_selling_only']['selling_agent'] ?? 35);
        $listingPercent = (float) ($scheme['resale_listing_only']['listing_agent'] ?? 15);
        $splits = [];

        if ($sale->listing_agent_id) {
            $splits = array_merge(
                $splits,
                $this->singleAgentSplit($sale->listing_agent_id, 'listing_agent', $listingPercent, $base),
            );
        }

        return array_merge(
            $splits,
            $this->singleAgentSplit($sale->assigned_to, 'selling_agent', $sellingPercent, $base),
        );
    }

    /** @return array<int, array{user_id:int, agent_role:string, percent:float, amount:float}> */
    protected function dualAgentSplits(Sale $sale, float $base, array $scheme): array
    {
        $listingPercent = (float) ($scheme['resale_dual']['listing_agent'] ?? 15);
        $sellingPercent = (float) ($scheme['resale_dual']['selling_agent'] ?? 35);

        $splits = [];
        if ($sale->listing_agent_id) {
            $splits = array_merge($splits, $this->singleAgentSplit($sale->listing_agent_id, 'listing_agent', $listingPercent, $base));
        }

        return array_merge($splits, $this->singleAgentSplit($sale->assigned_to, 'selling_agent', $sellingPercent, $base));
    }

    /** @return array<int, array{user_id:int, agent_role:string, percent:float, amount:float}> */
    protected function singleAgentSplit(?int $userId, string $role, float $percent, float $base): array
    {
        if (!$userId || $percent <= 0) {
            return [];
        }

        return [[
            'user_id' => $userId,
            'agent_role' => $role,
            'percent' => $percent,
            'amount' => round($base * ($percent / 100), 2),
        ]];
    }

    public function agentMetQuarterlyTarget(int $userId, $closeDate = null): bool
    {
        $contract = FreelanceAgentContract::activeForUser($userId);
        if (!$contract) {
            return false;
        }

        $date = $closeDate ? Carbon::parse($closeDate) : now();
        $start = $date->copy()->startOfQuarter();
        $end = $date->copy()->endOfQuarter();

        $query = Sale::query()
            ->where('assigned_to', $userId)
            ->where('stage', 'closed_won')
            ->where('transaction_type', 'primary')
            ->whereBetween('actual_close_date', [$start, $end]);

        if ($contract->quarterly_target_deals) {
            return $query->count() >= (int) $contract->quarterly_target_deals;
        }

        if ($contract->quarterly_target_amount) {
            $revenue = (float) $query->get()->sum(fn ($s) => (float) ($s->actual_value ?? $s->estimated_value ?? 0));

            return $revenue >= (float) $contract->quarterly_target_amount;
        }

        return false;
    }

    public function previewForSale(Sale $sale): array
    {
        $splits = $this->buildSplits($sale);
        $base = (float) ($sale->company_commission_amount ?? 0);
        $agentTotal = array_sum(array_column($splits, 'amount'));

        return [
            'transaction_type' => $this->resolveType($sale),
            'company_commission' => $base,
            'agent_total' => round($agentTotal, 2),
            'company_retained' => round(max(0, $base - $agentTotal), 2),
            'splits' => $splits,
        ];
    }

    public function eligibleForPayout(User $user, Sale $sale): bool
    {
        if (!$sale->commission_collected) {
            return false;
        }

        return FreelanceAgentContract::activeForUser($user->id) !== null;
    }
}
