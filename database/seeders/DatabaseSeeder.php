<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Each seeder only adds what is missing, so this is safe on a live server too
        $this->call([
            ConfigSeeder::class,
            RolePermissionSeeder::class,
            PassportClientSeeder::class,
            MenuSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            LandingContentSeeder::class,
            OfficerLookupSeeder::class,
            UserSeeder::class,
        ]);
    }
}
