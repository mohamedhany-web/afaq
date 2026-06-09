<?php

namespace App\Services\Freelance;

use App\Models\Sale;
use App\Models\SaleCommissionSplit;

class SaleCommissionSplitService
{
    public function __construct(protected FreelanceCommissionSchemeService $scheme) {}

    public function syncForSale(Sale $sale): void
    {
        if ($sale->stage !== 'closed_won') {
            $sale->commissionSplits()->delete();

            return;
        }

        $rows = $this->scheme->buildSplits($sale);
        $sale->commissionSplits()->delete();

        foreach ($rows as $row) {
            SaleCommissionSplit::create([
                'sale_id' => $sale->id,
                'user_id' => $row['user_id'],
                'agent_role' => $row['agent_role'],
                'percent_of_company' => $row['percent'],
                'amount' => $row['amount'],
                'payout_status' => $sale->commission_collected ? 'ready' : 'pending',
            ]);
        }

        if ($sale->commission_collected && $sale->commission_payout_status === 'pending') {
            $sale->update(['commission_payout_status' => 'ready']);
        }
    }

    public function markCollected(Sale $sale): void
    {
        $sale->update([
            'commission_collected' => true,
            'commission_collected_at' => now(),
            'commission_payout_status' => 'ready',
        ]);

        $sale->commissionSplits()->update(['payout_status' => 'ready']);
    }
}
