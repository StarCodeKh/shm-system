<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;

/**
 * Site settings from the config table (Settings › General), read anywhere with setting('config_limit').
 *
 * One query fills the cache; it stays until a setting changes (Configuration model events clear it),
 * so requests, queued jobs and console commands all read the same values without hitting the database.
 */
class Settings
{
    const CACHE_KEY = 'settings.all';

    // Used when a key is missing from the table (e.g. a fresh database before ConfigSeeder ran)
    const DEFAULTS = [
        'config_limit' => '20',
        'config_offset' => '0',
        'config_sort' => 'order_level',
        'config_order' => 'desc',
        'config_date_format' => 'Y-m-d',
        'config_otp_expire' => '2 minutes',
    ];

    // No in-memory copy on purpose: queue workers live for hours and must see a change straight away
    public static function all(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, fn () => Configuration::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, $default = null)
    {
        $value = static::all()[$key] ?? null;
        return $value ?? $default ?? (static::DEFAULTS[$key] ?? null);
    }

    /** Drop the cached copy; the next read loads the table again */
    public static function forget(): void
    {
        Cache::forget(static::CACHE_KEY);
    }
}
