<?php

namespace App\Support;

use App\Models\Project;

/**
 * ترقيم وحدات المشاريع العقارية — معيار آفاق
 * B.1 بدروم · GF.1 أرضي · FF.1 أول · SF.1 ثاني · TF.1 ثالث
 */
class ProjectUnitNumbering
{
    public static function floorPrefix(int $level): string
    {
        $levels = config('project_units.floor_levels', []);

        if (isset($levels[$level]['prefix'])) {
            return (string) $levels[$level]['prefix'];
        }

        if ($level >= 4) {
            return $level . 'F';
        }

        return 'F' . max(1, $level);
    }

    public static function floorLabel(int $level): string
    {
        $levels = config('project_units.floor_levels', []);

        if (isset($levels[$level]['label'])) {
            return (string) $levels[$level]['label'];
        }

        if ($level === -1) {
            return 'البدروم';
        }

        if ($level === 0) {
            return 'الدور الأرضي';
        }

        return 'الدور ' . $level;
    }

    public static function floorLabelEn(int $level): string
    {
        $levels = config('project_units.floor_levels', []);

        if (isset($levels[$level]['label_en'])) {
            return (string) $levels[$level]['label_en'];
        }

        return match ($level) {
            -1 => 'Basement',
            0 => 'Ground Floor',
            1 => 'First Floor',
            2 => 'Second Floor',
            3 => 'Third Floor',
            default => 'Floor ' . $level,
        };
    }

    public static function unitCode(int $floorLevel, int $sequence): string
    {
        return self::floorPrefix($floorLevel) . '.' . $sequence;
    }

    public static function isStandardUnitCode(string $code, int $floorLevel): bool
    {
        return (bool) preg_match(
            '/^' . preg_quote(self::floorPrefix($floorLevel), '/') . '\.\d+$/',
            $code
        );
    }

    public static function projectNeedsRenumbering(Project $project): bool
    {
        $project->loadMissing('buildingFloors.units');

        foreach ($project->buildingFloors as $floor) {
            if ($floor->label !== self::floorLabel((int) $floor->level)) {
                return true;
            }

            foreach ($floor->units as $unit) {
                if (!self::isStandardUnitCode($unit->code, (int) $floor->level)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @return array<int, array{prefix: string, label: string, label_en: string}> */
    public static function floorLevelsMap(): array
    {
        return config('project_units.floor_levels', []);
    }
}
