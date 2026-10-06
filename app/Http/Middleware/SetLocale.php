<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/**
 * Answers every request in the user's language.
 * The app sends X-Locale (kh | en) with each request; Khmer by default.
 */
class SetLocale
{
    private const SUPPORTED = ['kh', 'en'];

    public function handle($request, Closure $next)
    {
        $locale = $request->header('X-Locale') ?: substr((string) $request->header('Accept-Language'), 0, 2);
        if ($locale === 'km') {
            $locale = 'kh'; // ISO code for Khmer
        }
        App::setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : 'kh');

        return $next($request);
    }
}
