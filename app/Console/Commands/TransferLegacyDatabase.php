<?php

namespace App\Console\Commands;

use App\User;
use Database\Seeders\ConfigSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PassportClientSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Copies all data from the old database into the database built by the standard migrations.
 *
 * Both databases must be on the same MySQL server. The old database is only read.
 *
 *   DB_DATABASE=shm_new php artisan migrate --force
 *   DB_DATABASE=shm_new php artisan db:transfer-legacy shm
 *
 * Roles and permissions are not copied: the standard ones come from RolePermissionSeeder (Super Admin, Admin,
 * Normal User), and each user gets exactly one of them by name: the old users.role when it is one of the three,
 * else the least powerful of their old roles that is, else Normal User. Direct per-user permissions are dropped.
 * Tables that are not in the new schema (budget module, unused tables) are not copied.
 * Settings and officer form options that the old database lacks are added afterwards (seeders, missing rows only).
 *
 * Values are converted where the column type changed:
 *   - empty strings in columns that became numeric (status, *_id) become NULL
 *   - 0000-00-00 dates become NULL
 *   - text dates (e.g. "2023-11-19 03:50:57.79+00") become real datetimes
 */
class TransferLegacyDatabase extends Command
{
    protected $signature = 'db:transfer-legacy
        {source : Name of the old database on the same MySQL server}
        {--fresh : Empty each target table before copying (otherwise tables that already have rows are skipped)}';

    protected $description = 'Copy all data from the old database into the current (migrated) database and verify it';

    private const TEXT_TYPES = ['char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext'];

    private const NUMERIC_TYPES = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'float', 'double'];

    private const DATE_TYPES = ['date', 'datetime', 'timestamp'];

    private const RENAMED_TABLES = [
        'incentive_to_stakeholder' => 'incentive_to_stackholder',
    ];

    // Built from the seeders and the old user roles instead of copied (see above)
    private const NOT_COPIED = ['migrations', 'roles', 'permissions', 'permission_group', 'role_has_permissions', 'model_has_roles', 'model_has_permissions'];

    // Least powerful first
    private const ROLES = ['Normal User', 'Admin', 'Super Admin'];

    private const RENAMED_COLUMNS = [
        'incentive_to_stakeholder' => ['stakeholder' => 'stackholder'],
    ];

    public function handle()
    {
        $source = $this->argument('source');
        $target = DB::getDatabaseName();
        $migrations = DB::getTablePrefix().'migrations';

        if ($source === $target) {
            $this->error('The source and target databases are the same. Point DB_DATABASE at the new database.');

            return 1;
        }

        // Fail loudly instead of silently changing values
        DB::statement("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION'");
        // Tables are copied alphabetically, so a child can come before its parent.
        // The links were checked before the foreign keys were added; checks are back on at the end.
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $this->callSilent('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]);

        $prefix = DB::getTablePrefix();
        $tables = collect(DB::select(
            'select table_name as t from information_schema.tables where table_schema = ? order by table_name',
            [$target]
        ))->pluck('t')->reject(fn ($t) => in_array(substr($t, strlen($prefix)), self::NOT_COPIED, true));

        $failed = 0;
        foreach ($tables as $table) {
            $plain = substr($table, strlen($prefix));
            $sourceTable = isset(self::RENAMED_TABLES[$plain]) ? $prefix.self::RENAMED_TABLES[$plain] : $table;
            $renamedColumns = self::RENAMED_COLUMNS[$plain] ?? [];
            $sourceColumns = $this->columns($source, $sourceTable);
            if (! $sourceColumns) {
                $this->line("  <comment>skip</comment>  {$table} (not in {$source})");

                continue;
            }
            $targetColumns = $this->columns($target, $table);

            $existing = DB::table(DB::raw("`{$target}`.`{$table}`"))->count();
            if ($existing > 0 && ! $this->option('fresh')) {
                $this->line("  <comment>skip</comment>  {$table} (already has {$existing} rows, use --fresh to recopy)");

                continue;
            }
            if ($this->option('fresh')) {
                DB::statement("TRUNCATE TABLE `{$target}`.`{$table}`");
            }

            $names = [];
            $selects = [];
            foreach ($targetColumns as $column => $targetType) {
                $from = $renamedColumns[$column] ?? $column;
                if (! isset($sourceColumns[$from])) {
                    continue; // new column (added id / timestamps): let the default fill it
                }
                $names[] = "`{$column}`";
                $selects[] = $this->convert($from, $sourceColumns[$from], $targetType);
            }

            try {
                DB::statement(sprintf(
                    'INSERT INTO `%s`.`%s` (%s) SELECT %s FROM `%s`.`%s`',
                    $target, $table, implode(', ', $names), implode(', ', $selects), $source, $sourceTable
                ));
            } catch (\Throwable $e) {
                $failed++;
                $this->line("  <error>FAIL</error>  {$table}: ".strtok($e->getMessage(), "\n"));

                continue;
            }

            $problems = $this->verify($source, $target, $table, $sourceTable, $sourceColumns, $targetColumns);
            if ($problems) {
                $failed++;
                $this->line("  <error>CHECK</error> {$table}: ".implode('; ', $problems));
            } else {
                $rows = DB::table(DB::raw("`{$target}`.`{$table}`"))->count();
                $this->line("  <info>ok</info>    {$table} ({$rows} rows)");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->assignRoles($source);
        foreach ([StatusSeeder::class, ConfigSeeder::class, PassportClientSeeder::class, MenuSeeder::class] as $seeder) {
            $this->callSilent('db:seed', ['--class' => $seeder, '--force' => true]);
        }

        if ($failed) {
            $this->error("{$failed} table(s) need attention, see above.");

            return 1;
        }
        $this->info('All tables copied and verified.');

        return 0;
    }

    /** One standard role per copied user, from their old roles (see the class comment) */
    private function assignRoles(string $source): void
    {
        $prefix = DB::getTablePrefix();
        $type = (new User)->getMorphClass();
        $roleIds = DB::table('roles')->whereIn('name', self::ROLES)->pluck('id', 'name');
        $old = fn ($table) => "`{$source}`.`{$prefix}{$table}`";

        // Old role names per user, from the role links and from the old users.role column when it exists
        $linked = collect(DB::select("SELECT m.model_id AS user_id, r.name FROM {$old('model_has_roles')} m
            JOIN {$old('roles')} r ON r.id = m.role_id WHERE m.model_type LIKE '%User'"))->groupBy('user_id');
        $column = $this->columns($source, $prefix.'users');
        $named = isset($column['role']) ? DB::table(DB::raw($old('users')))->pluck('role', 'id') : collect();

        $counts = array_fill_keys(self::ROLES, 0);
        $fallback = 0;
        DB::table('model_has_roles')->where('model_type', $type)->delete();
        foreach (DB::table('users')->pluck('id') as $id) {
            $names = $linked->get($id, collect())->pluck('name')->all();
            $role = in_array($named->get($id), self::ROLES, true) ? $named->get($id)
                : (collect(self::ROLES)->first(fn ($r) => in_array($r, $names, true)));
            if (! $role) {
                $role = self::ROLES[0];
                $fallback++;
            }
            DB::table('model_has_roles')->insert(['role_id' => $roleIds[$role], 'model_type' => $type, 'model_id' => $id]);
            $counts[$role]++;
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $summary = collect($counts)->map(fn ($n, $r) => "{$r} {$n}")->implode(', ');
        $this->line("  <info>ok</info>    user roles: {$summary}".($fallback ? " ({$fallback} had no standard role: Normal User)" : ''));
    }

    /** column name => data type, in table order */
    private function columns(string $schema, string $table): array
    {
        return collect(DB::select(
            'select column_name as c, data_type as t from information_schema.columns where table_schema = ? and table_name = ? order by ordinal_position',
            [$schema, $table]
        ))->pluck('t', 'c')->all();
    }

    private function convert(string $column, string $from, string $to): string
    {
        $c = "`{$column}`";
        if (in_array($to, self::DATE_TYPES)) {
            if (in_array($from, self::TEXT_TYPES)) {
                return "CASE WHEN {$c} REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN LEFT({$c}, 19) END";
            }

            return "CASE WHEN CAST({$c} AS CHAR) LIKE '0000-00-00%' THEN NULL ELSE {$c} END";
        }
        if (in_array($to, self::NUMERIC_TYPES) && in_array($from, self::TEXT_TYPES)) {
            return "NULLIF(TRIM({$c}), '')";
        }

        return $c;
    }

    /** Row counts must match, and money columns must add up to the same total. */
    private function verify(string $source, string $target, string $table, string $sourceTable, array $sourceColumns, array $targetColumns): array
    {
        $problems = [];
        $before = DB::table(DB::raw("`{$source}`.`{$sourceTable}`"))->count();
        $after = DB::table(DB::raw("`{$target}`.`{$table}`"))->count();
        if ($before !== $after) {
            $problems[] = "rows {$before} -> {$after}";
        }
        foreach ($targetColumns as $column => $type) {
            if ($type !== 'decimal' || ! isset($sourceColumns[$column])) {
                continue;
            }
            $sum = fn ($schema, $t) => (float) DB::selectOne("select round(coalesce(sum(`{$column}`), 0), 2) as s from `{$schema}`.`{$t}`")->s;
            $a = $sum($source, $sourceTable);
            $b = $sum($target, $table);
            // float columns were stored with float rounding; allow 1 cent per 1000 rows
            if (abs($a - $b) > max(0.01, $before / 1000 * 0.01)) {
                $problems[] = "sum({$column}) {$a} -> {$b}";
            }
        }

        return $problems;
    }
}
