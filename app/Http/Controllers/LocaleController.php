<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, ['ar', 'en'], true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
