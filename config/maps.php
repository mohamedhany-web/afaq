<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key (اختياري — للعرض المدمج: قمر صناعي + Street View)
    |--------------------------------------------------------------------------
    | أنشئ المفتاح من: Google Cloud Console → Maps Embed API + Street View Static API
    | بدون المفتاح: تعمل روابط فتح Google Maps وStreet View في تبويب خارجي فقط.
    */
    'google_api_key' => env('GOOGLE_MAPS_API_KEY'),

    'default_heading' => 210,
    'default_pitch' => 10,
    'default_fov' => 90,
    'satellite_zoom' => 18,
];
