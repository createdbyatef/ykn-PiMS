<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED_LOCALES = ['ms', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale', 'ms'));

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'ms';
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
