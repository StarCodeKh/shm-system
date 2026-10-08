<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Site settings (tbl_config), from database/seeders/data/config.php.
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
        $now = now();
        $existing = DB::table('config')->pluck('key')->all();
        $rows = [];
        foreach (require __DIR__.'/data/config.php' as $key => $value) {
            if (in_array($key, $existing, true)) continue;
            $rows[] = ['code' => 'config', 'key' => $key, 'value' => $value, 'created_at' => $now, 'updated_at' => $now];
        }
        if ($rows) DB::table('config')->insert($rows);
        \App\Services\Settings::forget();
    }
}
