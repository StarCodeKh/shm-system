<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permissions: one row per name, and a Super Admin role that exists.
 *
 * - Merges duplicate permissions (same name + guard). The copy in an existing permission
 *   group is kept (the role editor lists permissions by group); roles and users that had
 *   any copy keep the permission.
 * - Adds the unique (name, guard_name) index Spatie expects, so duplicates can't come back.
 * - Creates the Super Admin role (config/access.php) if it is missing; it has every
 *   permission through CommonService::permission(), so it needs no rows of its own.
 */
return new class extends Migration
{
    public function up(): void
    {
        $permissions = config('permission.table_names.permissions');
        $roleHas = config('permission.table_names.role_has_permissions');
        $modelHas = config('permission.table_names.model_has_permissions');
        $groups = DB::table('permission_group')->pluck('id')->all();

        DB::transaction(function () use ($permissions, $roleHas, $modelHas, $groups) {
            $duplicates = DB::table($permissions)
                ->select('name', 'guard_name')
                ->groupBy('name', 'guard_name')
                ->havingRaw('count(*) > 1')
                ->get();

            foreach ($duplicates as $duplicate) {
                $copies = DB::table($permissions)
                    ->where('name', $duplicate->name)
                    ->where('guard_name', $duplicate->guard_name)
                    ->orderByDesc('id')
                    ->get();
                $keep = $copies->first(fn ($copy) => in_array($copy->group, $groups)) ?? $copies->first();
                $drop = $copies->pluck('id')->reject(fn ($id) => $id == $keep->id)->values()->all();

                DB::table($roleHas)->insertOrIgnore(
                    DB::table($roleHas)->whereIn('permission_id', $drop)->get()
                        ->map(fn ($row) => ['permission_id' => $keep->id, 'role_id' => $row->role_id])->all()
                );
                DB::table($modelHas)->insertOrIgnore(
                    DB::table($modelHas)->whereIn('permission_id', $drop)->get()
                        ->map(fn ($row) => ['permission_id' => $keep->id, 'model_type' => $row->model_type, 'model_id' => $row->model_id])->all()
                );
                DB::table($roleHas)->whereIn('permission_id', $drop)->delete();
                DB::table($modelHas)->whereIn('permission_id', $drop)->delete();
                DB::table($permissions)->whereIn('id', $drop)->delete();
            }
        });

        if (!Schema::hasIndex($permissions, ['name', 'guard_name'], 'unique')) {
            Schema::table($permissions, fn (Blueprint $table) => $table->unique(['name', 'guard_name']));
        }

        $roles = config('permission.table_names.roles');
        $superAdmin = config('access.super_admin_role');
        if ($superAdmin && !DB::table($roles)->where('name', $superAdmin)->exists()) {
            DB::table($roles)->insert([
                'name' => $superAdmin,
                'guard_name' => 'api',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // The merged duplicates are not recreated: they were the same permission
    public function down(): void
    {
        Schema::table(config('permission.table_names.permissions'), fn (Blueprint $table) => $table->dropUnique(['name', 'guard_name']));
    }
};
