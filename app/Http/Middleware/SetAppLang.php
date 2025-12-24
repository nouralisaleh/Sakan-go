<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetAppLang
{
    public function handle(Request $request, Closure $next)
    {
        $language = $request->header('Accept-Language')
            ?? $request->segment(1)
            ?? 'en';

        if (! in_array($language, config('app.available_locales'))) {
            $language = 'en';
        }

        App::setLocale($language);

        return $next($request);
    }
}
