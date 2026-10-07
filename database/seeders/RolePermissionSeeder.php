<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permission groups, permissions and the three roles: Super Admin, Admin, Normal User.
 * Data: database/seeders/data/permissions.php and roles.php.
 *
 * Safe to run more than once, also on a live server: php artisan db:seed --class=RolePermissionSeeder
 * It only adds what is missing. A role gets its default permissions only while it has none,
 * so changes made later in the role editor are kept.
 */
class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'api';

    public function run()
    {
        $groups = require __DIR__.'/data/permissions.php';
        $roles = require __DIR__.'/data/roles.php';
        $now = now();

        DB::transaction(function () use ($groups, $roles, $now) {
            $order = 0;
            foreach ($groups as $groupName => $permissions) {
                $groupId = null;
                if ($groupName !== '') {
                    $groupId = DB::table('permission_group')->where('name', $groupName)->value('id')
                        ?? DB::table('permission_group')->insertGetId(['name' => $groupName, 'order_level' => ++$order, 'created_at' => $now, 'updated_at' => $now]);
                }
                foreach ($permissions as $name) {
                    if (DB::table('permissions')->where('name', $name)->where('guard_name', self::GUARD)->exists()) continue;
                    DB::table('permissions')->insert(['group' => $groupId, 'name' => $name, 'guard_name' => self::GUARD, 'created_at' => $now, 'updated_at' => $now]);
                }
            }

            foreach ($roles as $roleName => $permissions) {
                $roleId = DB::table('roles')->where('name', $roleName)->where('guard_name', self::GUARD)->value('id')
                    ?? DB::table('roles')->insertGetId(['name' => $roleName, 'guard_name' => self::GUARD, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);

                if (!$permissions || DB::table('role_has_permissions')->where('role_id', $roleId)->exists()) continue;
                $ids = DB::table('permissions')->whereIn('name', $permissions)->where('guard_name', self::GUARD)->pluck('id');
                DB::table('role_has_permissions')->insert($ids->map(fn ($id) => ['permission_id' => $id, 'role_id' => $roleId])->all());
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
