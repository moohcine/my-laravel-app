<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    public function switch(string $locale)
    {
        $supported = array_keys(config('app.locales', []));

        if (!in_array($locale, $supported, true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        $referer = url()->previous();

        return redirect($referer ?: route('home'));
    }
}
