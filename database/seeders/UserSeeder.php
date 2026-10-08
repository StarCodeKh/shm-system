<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * One user per role, so a new server can be signed in to:
 *   superadmin@system.com (Super Admin), admin@system.com (Admin), user@system.com (Normal User)
 *
 * Password: SEED_USER_PASSWORD in .env. Only outside production it falls back to "Password@123",
 * so a known password never reaches a live server by accident. Change it after the first sign-in.
 *
 * Only on a new install: when the users table already has accounts (a live or transferred server), it does nothing,
 * so running db:seed on every deploy never adds these accounts there.
 * Also adds the Active / Inactive user statuses when they are missing.
 */
class UserSeeder extends Seeder
{
    private const USERS = [
        ['role' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@system.com', 'fullname' => 'Super Admin', 'latin' => 'SUPER ADMIN'],
        ['role' => 'Admin',       'username' => 'admin',      'email' => 'admin@system.com',      'fullname' => 'Admin',       'latin' => 'ADMIN'],
        ['role' => 'Normal User', 'username' => 'user',       'email' => 'user@system.com',       'fullname' => 'Normal User', 'latin' => 'NORMAL USER'],
    ];

    public function run()
    {
        foreach ([['Active', 'សកម្ម', 8], ['Inactive', 'អសកម្ម', 9]] as [$titleEn, $titleKh, $order]) {
            if (DB::table('mef_status')->where('group', 'STATUS')->where('title_en', $titleEn)->exists()) continue;
            DB::table('mef_status')->insert(['group' => 'STATUS', 'title_en' => $titleEn, 'title_kh' => $titleKh, 'order_level' => $order]);
        }

        if (User::query()->exists()) {
            return; // not a new install
        }

        $password = env('SEED_USER_PASSWORD') ?: (app()->environment('production') ? null : 'Password@123');
        if (!$password) {
            $this->command?->warn('Users not created: set SEED_USER_PASSWORD in .env, then run: php artisan db:seed --class=UserSeeder');
            return;
        }
        $active = DB::table('mef_status')->where('group', 'STATUS')->where('title_en', 'Active')->value('id');

        foreach (self::USERS as $row) {
            if (User::where('email', $row['email'])->orWhere('username', $row['username'])->exists()) continue;
            $user = User::create([
                'fullname' => $row['fullname'],
                'latin' => $row['latin'],
                'username' => $row['username'],
                'email' => $row['email'],
                'password' => bcrypt($password),
                'status' => $active,
            ]);
            $user->syncRoles([$row['role']]);
            $this->command?->info("{$row['role']}: {$row['email']}");
        }
    }
}
