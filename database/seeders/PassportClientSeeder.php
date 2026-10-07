<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

/**
 * The Passport "personal access" client that sign-in tokens are issued with (web and mobile login).
 * Same as: php artisan passport:client --personal --provider=users
 *
 * Safe to run more than once: does nothing when the client already exists (a transferred database keeps its own).
 */
class PassportClientSeeder extends Seeder
{
    public function run()
    {
        $clients = app(ClientRepository::class);
        try {
            $clients->personalAccessClient('users');
        } catch (\RuntimeException $e) {
            $clients->createPersonalAccessGrantClient(config('app.name').' Personal Access Client', 'users');
            $this->command?->info('Passport personal access client created');
        }
    }
}
