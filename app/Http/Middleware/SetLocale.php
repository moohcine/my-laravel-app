<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = array_keys(config('app.locales', []));
        $locale = session('locale', config('app.locale'));

        if (!$supported) {
            $supported = [config('app.locale')];
        }

        if (!in_array($locale, $supported, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
