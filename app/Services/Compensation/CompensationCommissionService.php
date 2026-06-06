<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompCommissionPlan;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Collection;

class CompensationCommissionService
{
    public function calculate(User $user, CompCommissionPlan $plan, CompPayrollPeriod $period): array
    {
        $sales = Sale::query()
            ->where('assigned_to', $user->id)
            ->where('stage', 'closed_won')
            ->whereBetween('actual_close_date', [$period->starts_at, $period->ends_at])
            ->get();

        $lines = [];
        $total = 0;

        foreach ($sales as $sale) {
            $amount = $this->commissionForSale($plan, $sale, $sales);
            $total += $amount;
            $lines[] = [
                'category' => 'commission',
                'label' => 'عمولة: ' . ($sale->client?->name ?? $sale->product_service),
                'amount' => $amount,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
            ];
        }

        return ['total' => round($total, 2), 'lines' => $lines];
    }

    protected function commissionForSale(CompCommissionPlan $plan, Sale $sale, Collection $periodSales): float
    {
        $value = (float) ($sale->actual_value ?? $sale->estimated_value ?? 0);
        $config = $plan->config ?? [];

        return match ($plan->model) {
            'percentage' => round($value * ((float) ($config['rate'] ?? 0) / 100), 2),
            'fixed_per_deal' => round((float) ($config['amount'] ?? 0), 2),
            'revenue_tier' => $this->tierCommission($value, $config['tiers'] ?? []),
            'hybrid' => round(
                $value * ((float) ($config['base_rate'] ?? 0) / 100)
                + (float) ($config['bonus_per_deal'] ?? 0),
                2
            ),
            default => 0,
        };
    }

    protected function tierCommission(float $value, array $tiers): float
    {
        $revenue = $value;
        foreach ($tiers as $tier) {
            $min = (float) ($tier['min'] ?? 0);
            $max = isset($tier['max']) ? (float) $tier['max'] : PHP_FLOAT_MAX;
            $rate = (float) ($tier['rate'] ?? 0);
            if ($revenue >= $min && $revenue <= $max) {
                return round($revenue * ($rate / 100), 2);
            }
        }

        return 0;
    }
}
