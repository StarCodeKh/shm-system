<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles: only Super Admin, Admin and Normal User.
 *
 * - Every other role is removed, with its permissions (tbl_role_has_permissions) and user links.
 * - A user who loses their role gets Normal User (the role with the least access).
 * - Normal User is created (without permissions) if a server does not have it yet.
 *
 * Safe on any server: works from the role names, does nothing when only these roles exist, and copies
 * everything it changes to the *_backup_20261007_roles tables first; down() puts it all back.
 * Once the change is confirmed on a server, those tables can be dropped.
 */
return new class extends Migration
{
    private const KEEP = ['Super Admin', 'Admin', 'Normal User'];
    private const FALLBACK = 'Normal User';
    private const GUARD = 'api';
    private const BACKUPS = [
        'roles' => 'roles_backup_20261007_roles',
        'role_has_permissions' => 'role_has_permissions_backup_20261007_roles',
        'model_has_roles' => 'model_has_roles_backup_20261007_roles',
    ];

    public function up(): void
    {
        $type = (new User)->getMorphClass();
        $remove = DB::table('roles')->whereNotIn('name', self::KEEP)->pluck('name', 'id');
        if ($remove->isEmpty()) {
            echo "  roles: nothing to remove\n";
            return;
        }

        $this->backup();

        $summary = DB::transaction(function () use ($type, $remove) {
            $fallback = DB::table('roles')->where('name', self::FALLBACK)->where('guard_name', self::GUARD)->value('id')
                ?? DB::table('roles')->insertGetId(['name' => self::FALLBACK, 'guard_name' => self::GUARD, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
            $removeIds = $remove->keys();

            // Users whose only role is going away
            $moved = DB::table('model_has_roles')->where('model_type', $type)->whereIn('role_id', $removeIds)
                ->whereNotIn('model_id', DB::table('model_has_roles')->where('model_type', $type)->whereNotIn('role_id', $removeIds)->select('model_id'))
                ->distinct()->pluck('model_id');
            foreach ($moved as $userId) {
                DB::table('model_has_roles')->insert(['role_id' => $fallback, 'model_type' => $type, 'model_id' => $userId]);
            }

            DB::table('model_has_roles')->whereIn('role_id', $removeIds)->delete();
            DB::table('role_has_permissions')->whereIn('role_id', $removeIds)->delete();
            DB::table('roles')->whereIn('id', $removeIds)->delete();

            return [
                'removed' => $remove->values()->implode(', '),
                'users moved to '.self::FALLBACK => $moved->count(),
            ];
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ($summary as $what => $value) echo "  roles: {$what}: {$value}\n";
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUPS['roles'])) return;

        // Whole tables back as they were before up(), ids included
        DB::transaction(function () {
            foreach (self::BACKUPS as $table => $backup) {
                DB::table($table)->delete();
                DB::statement("INSERT INTO {$this->name($table)} SELECT * FROM {$this->name($backup)}");
            }
        });
        foreach (self::BACKUPS as $backup) Schema::dropIfExists($backup);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // Copies the three tables up() changes, once (a re-run keeps the first copy)
    private function backup(): void
    {
        foreach (self::BACKUPS as $table => $backup) {
            if (Schema::hasTable($backup)) continue;
            DB::statement("CREATE TABLE {$this->name($backup)} LIKE {$this->name($table)}");
            DB::statement("INSERT INTO {$this->name($backup)} SELECT * FROM {$this->name($table)}");
        }
    }

    // Table name with the connection prefix, quoted (for raw SQL)
    private function name(string $table): string
    {
        return '`'.DB::getTablePrefix().$table.'`';
    }
};
