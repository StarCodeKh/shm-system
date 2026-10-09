<?php

use App\Services\Settings;

if (! function_exists('setting')) {
    /**
     * A site setting from the config table, e.g. setting('config_limit').
     * Falls back to $default, then to the built-in default in App\Services\Settings.
     */
    function setting(string $key, $default = null)
    {
        return Settings::get($key, $default);
    }
}
