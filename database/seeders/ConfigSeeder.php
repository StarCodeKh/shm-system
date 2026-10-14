<?php

namespace Database\Seeders;

use App\Services\Settings;
use Illuminate\Database\Seeder;

/**
 * Site settings (tbl_config), from database/seeders/data/config.php (code, key, value).
 * The app reads them with setting('config_limit') (App\\Services\\Settings, cached until a setting changes).
 *
 * Safe to run more than once: php artisan db:seed --class=ConfigSeeder
 * Only missing keys are added; values already set on a server are never changed.
 * Secrets (SMTP login, Firebase key) are seeded empty: set them on each server.
 */
class ConfigSeeder extends Seeder
{
    public function run()
    {
        SeedData::seed('config', $this->command);
        Settings::forget();
    }
}
