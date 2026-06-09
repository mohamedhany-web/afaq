<?php

namespace App\Helpers;

use App\Models\Project;

class MapLocationHelper
{
    /** إحداثيات افتراضية لخريطة التحرير — تُستخدم للعرض فقط ولا تُعتبر موقعاً للمشروع */
    public const DEFAULT_MAP_LAT = 30.0444;

    public const DEFAULT_MAP_LNG = 31.2357;

    public static function isPlaceholder(?float $lat, ?float $lng): bool
    {
        if ($lat === null || $lng === null) {
            return true;
        }

        $dlat = abs($lat - self::DEFAULT_MAP_LAT);
        $dlng = abs($lng - self::DEFAULT_MAP_LNG);

        return $dlat < 0.008 && $dlng < 0.008;
    }

    public static function hasReliableCoordinates(Project $project): bool
    {
        if (!$project->hasMapLocation()) {
            return false;
        }

        return !self::isPlaceholder((float) $project->latitude, (float) $project->longitude);
    }

    /** @return array{lat: float, lng: float}|null */
    public static function reliableCoordinates(Project $project): ?array
    {
        if (!self::hasReliableCoordinates($project)) {
            return null;
        }

        return [
            'lat' => (float) $project->latitude,
            'lng' => (float) $project->longitude,
        ];
    }
}
