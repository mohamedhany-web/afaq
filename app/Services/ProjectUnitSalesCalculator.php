<?php

namespace App\Services;

class ProjectUnitSalesCalculator
{
    /**
     * حساب إجمالي العقد وخطة السداد لنظام السيلز.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, float|int|null>
     */
    public static function calculate(array $input): array
    {
        $unitPrice = (float) ($input['unit_price_total'] ?? $input['price_cash'] ?? 0);
        $area = (float) ($input['area_m2'] ?? 0);
        $buildingPercent = (float) ($input['building_percent'] ?? 0);
        $discountPercent = (float) ($input['discount_percent'] ?? 0);
        $loadingPercent = (float) ($input['loading_percent'] ?? 0);
        $downPercent = (float) ($input['down_percent'] ?? 0);
        $maintenanceDeposit = (float) ($input['maintenance_deposit'] ?? 0);
        $years = (int) ($input['years'] ?? 0);
        $installmentMonths = (int) ($input['installment_months'] ?? ($years > 0 ? $years * 12 : 0));

        $afterDiscount = $unitPrice * (1 - ($discountPercent / 100));
        $withBuilding = $afterDiscount * (1 + ($buildingPercent / 100));
        $netUnitPrice = $withBuilding;
        $withLoading = $netUnitPrice * (1 + ($loadingPercent / 100));
        $totalContract = $withLoading + $maintenanceDeposit;
        $downPayment = $downPercent > 0 ? $totalContract * ($downPercent / 100) : (float) ($input['down_payment_amount'] ?? 0);
        $remaining = max(0, $totalContract - $downPayment);
        $installmentPerM2 = $area > 0 && $installmentMonths > 0
            ? round($remaining / ($area * $installmentMonths), 2)
            : null;

        return [
            'unit_price_total' => round($unitPrice, 2),
            'net_unit_price' => round($netUnitPrice, 2),
            'total_contract_amount' => round($totalContract, 2),
            'maintenance_deposit' => round($maintenanceDeposit, 2),
            'down_payment_amount' => round($downPayment, 2),
            'remaining_balance' => round($remaining, 2),
            'installment_per_m2' => $installmentPerM2,
            'installment_months' => $installmentMonths > 0 ? $installmentMonths : null,
            'building_percent' => $buildingPercent,
            'discount_percent' => $discountPercent,
            'loading_percent' => $loadingPercent,
            'down_percent' => $downPercent,
        ];
    }
}
