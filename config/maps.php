<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key (اختياري — لصفحات المشاركة القديمة فقط)
    |--------------------------------------------------------------------------
    | عارض CRM 3D يستخدم OpenStreetMap + Nominatim + Overpass بدون مفتاح.
    | المفتاح اختياري لعرض Google Embed في صفحات أخرى إن رغبت.
    */
    'google_api_key' => env('GOOGLE_MAPS_API_KEY'),

    'default_heading' => 210,
    'default_pitch' => 10,
    'default_fov' => 90,
    'satellite_zoom' => 18,
];
