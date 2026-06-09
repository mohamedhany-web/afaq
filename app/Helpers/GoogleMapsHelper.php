<?php

namespace App\Helpers;

class GoogleMapsHelper
{
    public static function apiKey(): ?string
    {
        $key = config('maps.google_api_key');

        return $key && trim($key) !== '' ? trim($key) : null;
    }

    public static function hasEmbedSupport(): bool
    {
        return self::apiKey() !== null;
    }

    public static function formatCoords(float $lat, float $lng): string
    {
        return round($lat, 7) . ',' . round($lng, 7);
    }

    /** فتح الموقع في Google Maps (تبويب خارجي) */
    public static function mapsUrl(float $lat, float $lng, ?string $label = null): string
    {
        $query = $label
            ? urlencode($label) . '@' . self::formatCoords($lat, $lng)
            : self::formatCoords($lat, $lng);

        return 'https://www.google.com/maps/search/?api=1&query=' . $query;
    }

    /** Street View حيوي — يفتح في Google Maps خارج النظام */
    public static function streetViewUrl(float $lat, float $lng): string
    {
        return 'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=' . self::formatCoords($lat, $lng);
    }

    /** اتجاهات القيادة إلى الموقع */
    public static function directionsUrl(float $lat, float $lng): string
    {
        return 'https://www.google.com/maps/dir/?api=1&destination=' . self::formatCoords($lat, $lng);
    }

    /** عرض القمر الصناعي — تبويب خارجي */
    public static function satelliteUrl(float $lat, float $lng): string
    {
        return 'https://www.google.com/maps/@' . self::formatCoords($lat, $lng) . ',18z/data=!3m1!1e3';
    }

    /** تضمين Street View داخل الصفحة (يتطلب API key) */
    public static function streetViewEmbedUrl(float $lat, float $lng): ?string
    {
        $key = self::apiKey();
        if (!$key) {
            return null;
        }

        $location = self::formatCoords($lat, $lng);
        $heading = (int) config('maps.default_heading', 210);
        $pitch = (int) config('maps.default_pitch', 10);
        $fov = (int) config('maps.default_fov', 90);

        return 'https://www.google.com/maps/embed/v1/streetview'
            . '?key=' . urlencode($key)
            . '&location=' . urlencode($location)
            . '&heading=' . $heading
            . '&pitch=' . $pitch
            . '&fov=' . $fov;
    }

    /** تضمين خريطة قمر صناعي (يتطلب API key) */
    public static function satelliteEmbedUrl(float $lat, float $lng): ?string
    {
        $key = self::apiKey();
        if (!$key) {
            return null;
        }

        $zoom = (int) config('maps.satellite_zoom', 18);

        return 'https://www.google.com/maps/embed/v1/view'
            . '?key=' . urlencode($key)
            . '&center=' . urlencode(self::formatCoords($lat, $lng))
            . '&zoom=' . $zoom
            . '&maptype=satellite';
    }

    /** تضمين خريطة عادية Google (يتطلب API key) */
    public static function placeEmbedUrl(float $lat, float $lng, ?string $label = null): ?string
    {
        $key = self::apiKey();
        if (!$key) {
            return null;
        }

        $q = $label
            ? urlencode($label) . '@' . self::formatCoords($lat, $lng)
            : self::formatCoords($lat, $lng);

        return 'https://www.google.com/maps/embed/v1/place'
            . '?key=' . urlencode($key)
            . '&q=' . $q
            . '&zoom=' . (int) config('maps.satellite_zoom', 18);
    }

    /** صورة قمر صناعي ثابتة — لنسيج أرض المشهد 3D (Static Maps API) */
    public static function staticSatelliteUrl(float $lat, float $lng, int $width = 640, int $height = 640, ?int $zoom = null): ?string
    {
        $key = self::apiKey();
        if (!$key) {
            return null;
        }

        $width = max(100, min(640, $width));
        $height = max(100, min(640, $height));
        $zoom = $zoom ?? (int) config('maps.satellite_zoom', 18);

        return 'https://maps.googleapis.com/maps/api/staticmap'
            . '?center=' . urlencode(self::formatCoords($lat, $lng))
            . '&zoom=' . $zoom
            . '&size=' . $width . 'x' . $height
            . '&maptype=satellite'
            . '&scale=2'
            . '&key=' . urlencode($key);
    }
}
