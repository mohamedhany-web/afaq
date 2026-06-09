<?php

namespace App\Services\Compensation;

use App\Models\Compensation\CompCommissionPlan;
use App\Models\Compensation\CompPayrollPeriod;
use App\Models\Sale;
use App\Models\SaleCommissionSplit;
use App\Models\User;
use App\Services\Freelance\FreelanceCommissionSchemeService;
use Illuminate\Support\Collection;

class CompensationCommissionService
{
    public function __construct(protected FreelanceCommissionSchemeService $freelanceScheme) {}

    public function calculate(User $user, CompCommissionPlan $plan, CompPayrollPeriod $period): array
    {
        if ($plan->model === 'freelance_scheme') {
            return $this->calculateFreelance($user, $period);
        }

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

    protected function calculateFreelance(User $user, CompPayrollPeriod $period): array
    {
        $splits = SaleCommissionSplit::query()
            ->where('user_id', $user->id)
            ->whereIn('payout_status', ['ready', 'paid'])
            ->whereHas('sale', function ($q) use ($period) {
                $q->where('stage', 'closed_won')
                    ->where('commission_collected', true)
                    ->whereBetween('commission_collected_at', [$period->starts_at, $period->ends_at]);
            })
            ->with('sale.client')
            ->get();

        $lines = [];
        $total = 0;

        foreach ($splits as $split) {
            $total += (float) $split->amount;
            $sale = $split->sale;
            $lines[] = [
                'category' => 'commission',
                'label' => 'عمولة وكيل: ' . ($sale?->client?->name ?? $sale?->product_service ?? 'صفقة #' . $split->sale_id),
                'amount' => (float) $split->amount,
                'reference_type' => Sale::class,
                'reference_id' => $split->sale_id,
            ];
        }

        return ['total' => round($total, 2), 'lines' => $lines];
    }
}
