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
            StatusSeeder::class, // first: every status column refers to it
            ConfigSeeder::class,
            RolePermissionSeeder::class,
            PassportClientSeeder::class,
            MenuSeeder::class,
            CurrencySeeder::class,
            PaymentSettingSeeder::class,
            OrganizationSeeder::class,
            AccountSeeder::class,
            ListSeeder::class,
            LanguageSeeder::class,
            LandingContentSeeder::class,
            UserSeeder::class,
        ]);
    }
}
