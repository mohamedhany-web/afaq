<?php

namespace App\Services;

use App\Models\BuildingFloor;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\UnitPaymentPlan;

class ProjectManualUnitService
{
    public function ensureManualFloor(Project $project): BuildingFloor
    {
        $existing = $project->buildingFloors()->where('label', 'يدوي')->first();
        if ($existing) {
            return $existing;
        }

        return $project->buildingFloors()->create([
            'level' => 0,
            'label' => 'يدوي',
            'height_m' => 3.6,
            'sort_order' => 0,
        ]);
    }

    /** @param  list<array<string, mixed>>  $rows */
    public function sync(Project $project, array $rows, bool $replace = false): int
    {
        if ($replace) {
            $project->units()
                ->where('status', '!=', ProjectUnit::STATUS_SOLD)
                ->where(function ($q) {
                    $q->whereNull('building_floor_id')
                        ->orWhereHas('floor', fn ($f) => $f->where('label', 'يدوي'));
                })
                ->delete();
        }

        $floor = $this->ensureManualFloor($project);
        $saved = 0;

        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $area = (float) ($row['area_m2'] ?? 0);
            if ($area <= 0) {
                continue;
            }

            $useType = $row['use_type'] ?? 'residential';
            $code = trim((string) ($row['apartment_number'] ?? $row['code'] ?? ''));
            if ($code === '') {
                $code = 'U-' . ($index + 1);
            }

            $unitPrice = (float) ($row['unit_price_total'] ?? $row['price_cash'] ?? 0);
            $calc = ProjectUnitSalesCalculator::calculate(array_merge($row, [
                'unit_price_total' => $unitPrice,
                'area_m2' => $area,
            ]));

            $unit = isset($row['id'])
                ? $project->units()->whereKey($row['id'])->first()
                : null;

            $payload = [
                'building_floor_id' => $floor->id,
                'code' => $code,
                'use_type' => $useType,
                'direction' => $row['direction'] ?? null,
                'floor_number' => $row['floor_number'] ?? null,
                'floor_label' => $row['floor_label'] ?? null,
                'apartment_number' => $row['apartment_number'] ?? $code,
                'area_m2' => $area,
                'price_cash' => $unitPrice,
                'price_installment' => $calc['total_contract_amount'],
                'unit_price_total' => $unitPrice,
                'status' => $row['status'] ?? ProjectUnit::STATUS_AVAILABLE,
            ];

            if ($unit) {
                $unit->update($payload);
            } else {
                $unit = $project->units()->create($payload);
            }

            $this->syncPaymentPlan($unit, $calc, $row);
            $saved++;
        }

        app(ProjectUnitGeneratorService::class)->syncProjectTotals($project);

        return $saved;
    }

    /** @param  array<string, mixed>  $calc
     * @param  array<string, mixed>  $row
     */
    protected function syncPaymentPlan(ProjectUnit $unit, array $calc, array $row): void
    {
        $plan = $unit->paymentPlans()->first();

        $data = [
            'plan_type' => UnitPaymentPlan::TYPE_INSTALLMENT,
            'building_percent' => $calc['building_percent'],
            'discount_percent' => $calc['discount_percent'],
            'loading_percent' => $calc['loading_percent'],
            'net_unit_price' => $calc['net_unit_price'],
            'total_contract_amount' => $calc['total_contract_amount'],
            'maintenance_deposit' => $calc['maintenance_deposit'],
            'down_percent' => $calc['down_percent'],
            'down_payment_amount' => $calc['down_payment_amount'],
            'remaining_balance' => $calc['remaining_balance'],
            'installment_months' => $calc['installment_months'],
            'years' => $calc['installment_months'] ? (int) ceil($calc['installment_months'] / 12) : null,
            'installment_per_m2' => $calc['installment_per_m2'],
            'notes' => $row['plan_notes'] ?? null,
        ];

        if ($plan) {
            $plan->update($data);
        } else {
            $unit->paymentPlans()->create($data);
        }
    }
}
