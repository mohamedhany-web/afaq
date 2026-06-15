<?php

namespace App\Services;

use App\Models\BuildingFloor;
use App\Models\Project;
use App\Models\Project3dScene;
use App\Models\ProjectUnit;
use App\Models\UnitPaymentPlan;
use App\Support\ProjectUnitNumbering;
use Illuminate\Support\Facades\DB;

class ProjectUnitGeneratorService
{
    /** @return array{floors: int, units: int, scene_version: int} */
    public function generate(Project $project, bool $replaceExisting = true): array
    {
        $config = $project->building_config ?? self::defaultConfigFor5B();

        if ($replaceExisting && $project->buildingFloors()->exists()) {
            $this->clearBuilding($project);
        }

        return DB::transaction(function () use ($project, $config) {
            $floors = $this->createFloors($project, $config);
            $unitCount = 0;
            $zOffset = 0.0;

            foreach ($floors as $floor) {
                $definitions = $this->unitDefinitionsForFloor($floor, $config);
                $count = count($definitions);

                foreach ($definitions as $index => $def) {
                    $area = $def['area_m2'];
                    $pricing = $this->priceUnit($def['use_type'], $area, $floor->level, $config);
                    $layout = $this->layoutUnitMesh($index, $count, $floor, $config);
                    $interior = $this->interiorLayout($def['use_type'], $area);

                    $unit = ProjectUnit::create([
                        'project_id' => $project->id,
                        'building_floor_id' => $floor->id,
                        'code' => $def['code'],
                        'use_type' => $def['use_type'],
                        'area_m2' => $area,
                        'price_cash' => $pricing['cash'],
                        'price_installment' => $pricing['installment'],
                        'status' => ProjectUnit::STATUS_AVAILABLE,
                        'mesh_x' => $layout['x'],
                        'mesh_y' => $zOffset,
                        'mesh_z' => $layout['z'],
                        'mesh_w' => $layout['w'],
                        'mesh_h' => (float) $floor->height_m,
                        'mesh_d' => $layout['d'],
                        'meta' => [
                            'floor_level' => $floor->level,
                            'slot_index' => $index,
                            'interior' => $interior,
                        ],
                    ]);

                    $this->attachPaymentPlans($unit, $def['use_type'], $area, $pricing, $config);
                    $unitCount++;
                }

                $zOffset += (float) $floor->height_m;
            }

            $this->syncProjectTotals($project);
            $scene = $this->upsertScene($project, $config, $zOffset);

            return [
                'floors' => count($floors),
                'units' => $unitCount,
                'scene_version' => $scene->version,
            ];
        });
    }

    public static function defaultConfigFor5B(): array
    {
        return [
            'template' => 'tower_mixed_5b',
            'structure' => [
                'basement' => [
                    'enabled' => true,
                    'height_m' => 3.2,
                    'commercial_units' => 3,
                    'administrative_units' => 1,
                    'commercial_area' => [65, 120],
                ],
                'ground' => [
                    'height_m' => 4.5,
                    'commercial_units' => 5,
                    'administrative_units' => 1,
                    'commercial_area' => [42, 60],
                ],
                'residential_floors' => 3,
                'residential' => [
                    'units_per_floor' => 4,
                    'height_m' => 3.6,
                    'area_range' => [90, 170],
                    'areas' => [120, 95, 140, 110],
                ],
            ],
            'pricing' => [
                'residential' => [
                    'cash_per_m2_by_level' => [
                        '-1' => 20000,
                        '0' => 20500,
                        '1' => 21000,
                        '2' => 22000,
                        '3' => 23000,
                        '4' => 24500,
                    ],
                    'down_payment_min' => 1000000,
                ],
                'commercial' => [
                    'cash_per_m2_min' => 95000,
                    'cash_per_m2_max' => 110000,
                    'installment_per_m2_min' => 180000,
                    'installment_per_m2_max' => 250000,
                    'down_percent' => 25,
                    'years_max' => 4,
                ],
                'administrative' => [
                    'cash_per_m2' => 85000,
                    'down_percent' => 25,
                    'years_max' => 4,
                    'installment_per_m2' => 150000,
                ],
            ],
            'scene' => [
                'land_width' => 72,
                'land_depth' => 58,
                'building_width' => 24,
                'building_depth' => 16,
            ],
        ];
    }

    protected function clearBuilding(Project $project): void
    {
        $project->units()->each(function (ProjectUnit $unit) {
            $unit->paymentPlans()->delete();
        });
        $project->units()->delete();
        $project->buildingFloors()->delete();
        $project->scene3d()->delete();
    }

    /** @return array<int, BuildingFloor> */
    protected function createFloors(Project $project, array $config): array
    {
        $structure = $config['structure'] ?? [];
        $floors = [];
        $sort = 0;

        if ($structure['basement']['enabled'] ?? false) {
            $floors[] = BuildingFloor::create([
                'project_id' => $project->id,
                'level' => -1,
                'label' => ProjectUnitNumbering::floorLabel(-1),
                'height_m' => $structure['basement']['height_m'] ?? 3.2,
                'use_mix' => ['commercial', 'administrative'],
                'sort_order' => $sort++,
            ]);
        }

        $floors[] = BuildingFloor::create([
            'project_id' => $project->id,
            'level' => 0,
            'label' => ProjectUnitNumbering::floorLabel(0),
            'height_m' => $structure['ground']['height_m'] ?? 4.5,
            'use_mix' => ['commercial', 'administrative'],
            'sort_order' => $sort++,
        ]);

        $resFloors = (int) ($structure['residential_floors'] ?? 0);
        for ($i = 1; $i <= $resFloors; $i++) {
            $floors[] = BuildingFloor::create([
                'project_id' => $project->id,
                'level' => $i,
                'label' => ProjectUnitNumbering::floorLabel($i),
                'height_m' => $structure['residential']['height_m'] ?? 3.6,
                'use_mix' => ['residential'],
                'sort_order' => $sort++,
            ]);
        }

        return $floors;
    }

    /** @return array<int, array{code: string, use_type: string, area_m2: float}> */
    protected function unitDefinitionsForFloor(BuildingFloor $floor, array $config): array
    {
        $structure = $config['structure'] ?? [];
        $specs = [];

        if ($floor->level === -1) {
            $basement = $structure['basement'] ?? [];
            $range = $basement['commercial_area'] ?? [65, 120];
            for ($i = 1; $i <= (int) ($basement['commercial_units'] ?? 0); $i++) {
                $specs[] = [
                    'use_type' => ProjectUnit::USE_COMMERCIAL,
                    'area_m2' => $this->varyArea($range, $i),
                ];
            }
            for ($i = 1; $i <= (int) ($basement['administrative_units'] ?? 0); $i++) {
                $specs[] = [
                    'use_type' => ProjectUnit::USE_ADMINISTRATIVE,
                    'area_m2' => 80 + ($i * 5),
                ];
            }
        } elseif ($floor->level === 0) {
            $ground = $structure['ground'] ?? [];
            $range = $ground['commercial_area'] ?? [42, 60];
            for ($i = 1; $i <= (int) ($ground['commercial_units'] ?? 0); $i++) {
                $specs[] = [
                    'use_type' => ProjectUnit::USE_COMMERCIAL,
                    'area_m2' => $this->varyArea($range, $i),
                ];
            }
            for ($i = 1; $i <= (int) ($ground['administrative_units'] ?? 0); $i++) {
                $specs[] = [
                    'use_type' => ProjectUnit::USE_ADMINISTRATIVE,
                    'area_m2' => 90,
                ];
            }
        } else {
            $res = $structure['residential'] ?? [];
            $areas = $res['areas'] ?? [120, 95, 140, 110];
            $perFloor = (int) ($res['units_per_floor'] ?? 4);

            for ($i = 0; $i < $perFloor; $i++) {
                $specs[] = [
                    'use_type' => ProjectUnit::USE_RESIDENTIAL,
                    'area_m2' => (float) ($areas[$i] ?? $this->varyArea($res['area_range'] ?? [90, 170], $i + 1)),
                ];
            }
        }

        $sequence = 1;
        foreach ($specs as &$spec) {
            $spec['code'] = ProjectUnitNumbering::unitCode($floor->level, $sequence++);
        }
        unset($spec);

        return $specs;
    }

    /** @return array{x: float, y: float, z: float, w: float, d: float} */
    protected function layoutUnitMesh(int $index, int $total, BuildingFloor $floor, array $config): array
    {
        $bw = (float) ($config['scene']['building_width'] ?? 24);
        $bd = (float) ($config['scene']['building_depth'] ?? 16);
        $cols = $floor->level >= 1 ? 2 : min(max($total, 1), 4);
        $rows = (int) max(1, ceil($total / $cols));
        $col = $index % $cols;
        $row = intdiv($index, $cols);
        $slotW = $bw / $cols;
        $slotD = $bd / $rows;
        $inset = 0.12;

        return [
            'x' => round(-$bw / 2 + $slotW * $col + $slotW / 2, 2),
            'z' => round(-$bd / 2 + $slotD * $row + $slotD / 2, 2),
            'w' => round(max(2.5, $slotW * (1 - $inset)), 2),
            'd' => round(max(2.2, $slotD * (1 - $inset)), 2),
        ];
    }

    /** @return array{rooms: array<int, array<string, mixed>>, area_m2: float} */
    public function interiorLayout(string $useType, float $area): array
    {
        return match ($useType) {
            ProjectUnit::USE_COMMERCIAL => [
                'area_m2' => $area,
                'rooms' => [
                    ['id' => 'hall', 'label' => 'صالة عرض', 'x' => 0.05, 'z' => 0.08, 'w' => 0.65, 'd' => 0.75, 'color' => '#fed7aa', 'type' => 'commercial'],
                    ['id' => 'storage', 'label' => 'مخزن', 'x' => 0.72, 'z' => 0.08, 'w' => 0.22, 'd' => 0.35, 'color' => '#e2e8f0', 'type' => 'storage'],
                    ['id' => 'wc', 'label' => 'حمام', 'x' => 0.72, 'z' => 0.48, 'w' => 0.22, 'd' => 0.35, 'color' => '#bae6fd', 'type' => 'bath'],
                ],
            ],
            ProjectUnit::USE_ADMINISTRATIVE => [
                'area_m2' => $area,
                'rooms' => [
                    ['id' => 'reception', 'label' => 'استقبال', 'x' => 0.05, 'z' => 0.55, 'w' => 0.9, 'd' => 0.38, 'color' => '#ddd6fe', 'type' => 'office'],
                    ['id' => 'office1', 'label' => 'مكتب 1', 'x' => 0.05, 'z' => 0.08, 'w' => 0.42, 'd' => 0.42, 'color' => '#c4b5fd', 'type' => 'office'],
                    ['id' => 'office2', 'label' => 'مكتب 2', 'x' => 0.52, 'z' => 0.08, 'w' => 0.42, 'd' => 0.42, 'color' => '#c4b5fd', 'type' => 'office'],
                ],
            ],
            default => $this->residentialInterior($area),
        };
    }

    /** @return array{rooms: array<int, array<string, mixed>>, area_m2: float} */
    protected function residentialInterior(float $area): array
    {
        $rooms = [
            ['id' => 'living', 'label' => 'صالة', 'x' => 0.04, 'z' => 0.42, 'w' => 0.52, 'd' => 0.52, 'color' => '#fef08a', 'type' => 'living'],
            ['id' => 'kitchen', 'label' => 'مطبخ', 'x' => 0.58, 'z' => 0.42, 'w' => 0.36, 'd' => 0.3, 'color' => '#fecaca', 'type' => 'kitchen'],
            ['id' => 'master', 'label' => 'غرفة رئيسية', 'x' => 0.04, 'z' => 0.06, 'w' => 0.36, 'd' => 0.32, 'color' => '#bfdbfe', 'type' => 'bedroom'],
            ['id' => 'bath', 'label' => 'حمام', 'x' => 0.58, 'z' => 0.06, 'w' => 0.2, 'd' => 0.28, 'color' => '#a5f3fc', 'type' => 'bath'],
            ['id' => 'balcony', 'label' => 'بلكونة', 'x' => 0.8, 'z' => 0.06, 'w' => 0.16, 'd' => 0.88, 'color' => '#bbf7d0', 'type' => 'balcony'],
        ];

        if ($area >= 110) {
            $rooms[] = ['id' => 'bed2', 'label' => 'غرفة نوم 2', 'x' => 0.42, 'z' => 0.06, 'w' => 0.14, 'd' => 0.32, 'color' => '#93c5fd', 'type' => 'bedroom'];
        }

        if ($area >= 150) {
            $rooms[] = ['id' => 'bed3', 'label' => 'غرفة نوم 3', 'x' => 0.22, 'z' => 0.74, 'w' => 0.3, 'd' => 0.2, 'color' => '#93c5fd', 'type' => 'bedroom'];
        }

        return ['area_m2' => $area, 'rooms' => $rooms];
    }

    protected function varyArea(array $range, int $seed): float
    {
        $min = (float) ($range[0] ?? 40);
        $max = (float) ($range[1] ?? $min);
        if ($max <= $min) {
            return $min;
        }

        $step = ($max - $min) / 5;
        $offset = ($seed % 5) * $step;

        return round($min + $offset, 2);
    }

    /** @return array{cash: float, installment: ?float, cash_per_m2: float, installment_per_m2: ?float} */
    protected function priceUnit(string $useType, float $area, int $level, array $config): array
    {
        $pricing = $config['pricing'] ?? [];

        if ($useType === ProjectUnit::USE_RESIDENTIAL) {
            $rules = $pricing['residential'] ?? [];
            $perM2 = (float) ($rules['cash_per_m2_by_level'][(string) $level] ?? 22000);
            $cash = round($area * $perM2, 2);

            return [
                'cash' => max($cash, (float) ($rules['down_payment_min'] ?? 0)),
                'installment' => null,
                'cash_per_m2' => $perM2,
                'installment_per_m2' => null,
            ];
        }

        if ($useType === ProjectUnit::USE_COMMERCIAL) {
            $rules = $pricing['commercial'] ?? [];
            $cashPerM2 = (float) ($rules['cash_per_m2_min'] ?? 95000);
            $instPerM2 = (float) ($rules['installment_per_m2_min'] ?? 180000);

            return [
                'cash' => round($area * $cashPerM2, 2),
                'installment' => round($area * $instPerM2, 2),
                'cash_per_m2' => $cashPerM2,
                'installment_per_m2' => $instPerM2,
            ];
        }

        $rules = $pricing['administrative'] ?? [];
        $cashPerM2 = (float) ($rules['cash_per_m2'] ?? 85000);
        $instPerM2 = (float) ($rules['installment_per_m2'] ?? 150000);

        return [
            'cash' => round($area * $cashPerM2, 2),
            'installment' => round($area * $instPerM2, 2),
            'cash_per_m2' => $cashPerM2,
            'installment_per_m2' => $instPerM2,
        ];
    }

    protected function attachPaymentPlans(ProjectUnit $unit, string $useType, float $area, array $pricing, array $config): void
    {
        UnitPaymentPlan::create([
            'project_unit_id' => $unit->id,
            'plan_type' => UnitPaymentPlan::TYPE_CASH,
            'down_payment_amount' => $useType === ProjectUnit::USE_RESIDENTIAL
                ? ($config['pricing']['residential']['down_payment_min'] ?? null)
                : null,
            'notes' => 'سعر كاش',
        ]);

        if ($pricing['installment'] === null) {
            return;
        }

        $rules = $useType === ProjectUnit::USE_COMMERCIAL
            ? ($config['pricing']['commercial'] ?? [])
            : ($config['pricing']['administrative'] ?? []);

        UnitPaymentPlan::create([
            'project_unit_id' => $unit->id,
            'plan_type' => UnitPaymentPlan::TYPE_INSTALLMENT,
            'down_percent' => $rules['down_percent'] ?? 25,
            'years' => $rules['years_max'] ?? 4,
            'installment_per_m2' => $pricing['installment_per_m2'],
            'down_payment_amount' => round($pricing['installment'] * (($rules['down_percent'] ?? 25) / 100), 2),
            'notes' => 'نظام سداد حتى ' . ($rules['years_max'] ?? 4) . ' سنوات',
        ]);
    }

    public function syncProjectTotals(Project $project): void
    {
        $total = $project->units()->count();
        $sold = $project->units()->where('status', ProjectUnit::STATUS_SOLD)->count();
        $reserved = $project->units()->where('status', ProjectUnit::STATUS_RESERVED)->count();

        $priceFrom = $project->units()->min('price_cash');
        $priceTo = $project->units()->max('price_cash');

        $project->update([
            'total_units' => $total,
            'sold_units' => $sold,
            'available_units' => max(0, $total - $sold - $reserved),
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
        ]);
    }

    protected function upsertScene(Project $project, array $config, float $totalHeight): Project3dScene
    {
        $sceneCfg = $config['scene'] ?? [];
        $units = $project->units()->with('floor')->get();
        $landW = (float) ($sceneCfg['land_width'] ?? 72);
        $landD = (float) ($sceneCfg['land_depth'] ?? 58);

        return Project3dScene::updateOrCreate(
            ['project_id' => $project->id],
            [
                'version' => (int) (Project3dScene::where('project_id', $project->id)->value('version') ?? 0) + 1,
                'camera_config' => [
                    'land' => ['position' => [55, 42, 55], 'target' => [0, 0, 0], 'fov' => 42],
                    'building' => ['position' => [32, 22, 32], 'target' => [0, $totalHeight / 2, 0], 'fov' => 45],
                    'interior' => ['position' => [0, 4.5, 6], 'target' => [0, 1.2, 0], 'fov' => 50],
                ],
                'scene_config' => [
                    'land_width' => $landW,
                    'land_depth' => $landD,
                    'land_area_m2' => (float) ($project->land_area_m2 ?? 0),
                    'building_width' => (float) ($sceneCfg['building_width'] ?? 24),
                    'building_depth' => (float) ($sceneCfg['building_depth'] ?? 16),
                    'total_height' => $totalHeight,
                    'project_name' => $project->name,
                    'units' => $units->map(fn (ProjectUnit $u) => [
                        'id' => $u->id,
                        'code' => $u->code,
                        'use_type' => $u->use_type,
                        'status' => $u->status,
                        'floor_label' => $u->floor?->label,
                        'color' => $u->meshColor(),
                        'position' => [(float) $u->mesh_x, (float) $u->mesh_y, (float) $u->mesh_z],
                        'size' => [(float) $u->mesh_w, (float) $u->mesh_h, (float) $u->mesh_d],
                        'area_m2' => (float) $u->area_m2,
                        'price_cash' => (float) $u->price_cash,
                        'interior' => $u->meta['interior'] ?? $this->interiorLayout($u->use_type, (float) $u->area_m2),
                    ])->values()->all(),
                ],
                'generated_at' => now(),
            ],
        );
    }

    /** @return array<string, mixed> */
    /** إعادة ترقيم الوحدات والطوابق حسب معيار آفاق (B.1 · GF.1 · FF.1 …) */
    public function applyAfaqNumbering(Project $project): int
    {
        $updated = 0;

        $floors = $project->buildingFloors()
            ->orderBy('sort_order')
            ->with(['units' => fn ($q) => $q->orderBy('id')])
            ->get();

        foreach ($floors as $floor) {
            $level = (int) $floor->level;
            $label = ProjectUnitNumbering::floorLabel($level);

            if ($floor->label !== $label) {
                $floor->update(['label' => $label]);
            }

            $sequence = 1;
            foreach ($floor->units as $unit) {
                $newCode = ProjectUnitNumbering::unitCode($level, $sequence++);
                if ($unit->code !== $newCode) {
                    $unit->update(['code' => $newCode]);
                    $updated++;
                }
            }
        }

        if ($updated > 0 && $project->scene3d) {
            $config = $project->building_config ?? self::defaultConfigFor5B();
            $totalHeight = (float) $floors->sum('height_m');
            $this->upsertScene($project, $config, $totalHeight);
        }

        return $updated;
    }

    public function buildingSummary(Project $project): array
    {
        $units = $project->units;

        return [
            'floors_count' => $project->buildingFloors()->count(),
            'units_count' => $units->count(),
            'by_status' => $units->groupBy('status')->map->count()->all(),
            'by_use' => $units->groupBy('use_type')->map->count()->all(),
            'has_scene' => $project->scene3d !== null,
            'scene_version' => $project->scene3d?->version,
        ];
    }
}
