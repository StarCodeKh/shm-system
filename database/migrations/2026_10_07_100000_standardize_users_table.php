<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Users: one role per user, kept only in Spatie (tbl_model_has_roles), and no duplicate columns.
 *
 * - A user with several roles keeps the one named in users.role (else the lowest role id).
 * - A user with no role gets the one named in users.role, else the one in users.role_id, when it exists.
 * - Removes role rows of users that no longer exist.
 * - Removes permissions granted to users one by one: access comes from the role only
 *   (CommonService::permission() reads permissions via roles; the direct grants were never used).
 * - Drops columns that copied other data or were never used:
 *     role, role_id  -> the Spatie role (User::$role is computed from it)
 *     entity         -> entity_id
 *     is_active      -> status
 *     duty, is_default
 *
 * Safe on any server: every step checks what is there first, so it can run on data that differs from
 * local, or again after a partial run. Everything it removes is copied to the *_backup_20261007 tables
 * first, and down() puts it all back. Once the change is confirmed on a server, those tables can be dropped.
 */
return new class extends Migration
{
    private const COLUMNS = ['role', 'role_id', 'entity', 'duty', 'is_default', 'is_active'];
    private const USERS_BACKUP = 'users_backup_20261007';
    private const ROLES_BACKUP = 'model_has_roles_backup_20261007';
    private const PERMISSIONS_BACKUP = 'model_has_permissions_backup_20261007';

    public function up(): void
    {
        $type = (new User)->getMorphClass();
        $columns = array_values(array_filter(self::COLUMNS, fn ($column) => Schema::hasColumn('users', $column)));

        $this->backup($type, $columns);

        $summary = DB::transaction(function () use ($type, $columns) {
            $summary = ['orphan role rows removed' => 0, 'extra roles removed' => 0, 'roles assigned' => 0, 'direct permissions removed' => 0];
            $roleIds = DB::table('roles')->where('guard_name', 'api')->pluck('id', 'name');
            $users = DB::table('users')->select(array_merge(['id'], array_intersect(['role', 'role_id'], $columns)))->get();

            $summary['orphan role rows removed'] = DB::table('model_has_roles')->where('model_type', $type)
                ->whereNotIn('model_id', $users->pluck('id'))->delete();

            foreach ($users as $user) {
                $current = DB::table('model_has_roles')->where('model_type', $type)->where('model_id', $user->id)
                    ->orderBy('role_id')->pluck('role_id');
                $named = $roleIds[$user->role ?? ''] ?? null;

                if ($current->isEmpty()) {
                    $assign = $named ?: ($roleIds->contains($user->role_id ?? null) ? $user->role_id : null);
                    if ($assign) {
                        DB::table('model_has_roles')->insert(['role_id' => $assign, 'model_type' => $type, 'model_id' => $user->id]);
                        $summary['roles assigned']++;
                    }
                } elseif ($current->count() > 1) {
                    $keep = $current->contains($named) ? $named : $current->first();
                    $summary['extra roles removed'] += DB::table('model_has_roles')->where('model_type', $type)
                        ->where('model_id', $user->id)->where('role_id', '!=', $keep)->delete();
                }
            }

            $summary['direct permissions removed'] = DB::table('model_has_permissions')->where('model_type', $type)->delete();
            return $summary;
        });

        Schema::table('users', function (Blueprint $table) use ($columns) {
            if (Schema::hasIndex('users', ['role_id'])) $table->dropIndex(['role_id']);
            if ($columns) $table->dropColumn($columns);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $summary['columns dropped'] = $columns ? implode(', ', $columns) : 'none';
        foreach ($summary as $what => $count) echo "  users: {$what}: {$count}\n";
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'entity')) $table->string('entity', 120)->nullable()->after('entity_id');
            if (!Schema::hasColumn('users', 'role_id')) $table->unsignedBigInteger('role_id')->nullable()->index()->after('entity');
            if (!Schema::hasColumn('users', 'role')) $table->string('role', 255)->nullable()->after('role_id');
            if (!Schema::hasColumn('users', 'duty')) $table->string('duty', 120)->nullable()->after('department');
            if (!Schema::hasColumn('users', 'is_default')) $table->tinyInteger('is_default')->nullable()->default(0)->after('dob');
            if (!Schema::hasColumn('users', 'is_active')) $table->tinyInteger('is_active')->nullable()->default(1)->after('is_default');
        });

        if (!Schema::hasTable(self::USERS_BACKUP)) return;

        $type = (new User)->getMorphClass();
        DB::transaction(function () use ($type) {
            foreach (DB::table(self::USERS_BACKUP)->get() as $row) {
                $values = array_intersect_key((array) $row, array_flip(self::COLUMNS));
                DB::table('users')->where('id', $row->id)->update($values);
            }
            // Whole rows back, ids included
            foreach (['model_has_roles' => self::ROLES_BACKUP, 'model_has_permissions' => self::PERMISSIONS_BACKUP] as $table => $backup) {
                DB::table($table)->where('model_type', $type)->delete();
                DB::statement("INSERT INTO {$this->name($table)} SELECT * FROM {$this->name($backup)}");
            }
        });

        Schema::dropIfExists(self::USERS_BACKUP);
        Schema::dropIfExists(self::ROLES_BACKUP);
        Schema::dropIfExists(self::PERMISSIONS_BACKUP);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // Copies what up() changes, once (a re-run keeps the first copy)
    private function backup(string $type, array $columns): void
    {
        if (Schema::hasTable(self::USERS_BACKUP)) return;

        Schema::create(self::USERS_BACKUP, function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('role', 255)->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('entity', 120)->nullable();
            $table->string('duty', 120)->nullable();
            $table->tinyInteger('is_default')->nullable();
            $table->tinyInteger('is_active')->nullable();
        });
        DB::table(self::USERS_BACKUP)->insertUsing(array_merge(['id'], $columns), DB::table('users')->select(array_merge(['id'], $columns)));

        // Same structure as the source table (servers may have extra columns such as id / timestamps)
        foreach (['model_has_roles' => self::ROLES_BACKUP, 'model_has_permissions' => self::PERMISSIONS_BACKUP] as $table => $backup) {
            DB::statement("CREATE TABLE {$this->name($backup)} LIKE {$this->name($table)}");
            DB::statement("INSERT INTO {$this->name($backup)} SELECT * FROM {$this->name($table)} WHERE model_type = ?", [$type]);
        }
    }

    // Table name with the connection prefix, quoted (for raw SQL)
    private function name(string $table): string
    {
        return '`'.DB::getTablePrefix().$table.'`';
    }
};
