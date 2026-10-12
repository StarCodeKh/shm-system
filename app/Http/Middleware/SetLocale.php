<?php

namespace App\Http\Middleware;

use App\Models\Settings\Language;
use Closure;
use Illuminate\Support\Facades\App;

/**
 * Answers every request in the user's language.
 * The app sends X-Locale (one of the active languages, Settings › Language) with each request;
 * otherwise the default language (the first active one, Khmer when there are none yet).
 * Server texts missing in a language show in English (fallback_locale).
 */
class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = strtolower((string) ($request->header('X-Locale') ?: substr((string) $request->header('Accept-Language'), 0, 2)));
        if ($locale === 'km') {
            $locale = 'kh'; // ISO code for Khmer
        }
        $supported = $this->supported();
        App::setLocale(in_array($locale, $supported, true) ? $locale : ($supported[0] ?? 'kh'));

        return $next($request);
    }

    private function supported(): array
    {
        try {
            return Language::codes() ?: ['kh', 'en'];
        } catch (\Throwable $e) {
            return ['kh', 'en']; // before the language table exists (fresh install, migrations)
        }
    }
}
