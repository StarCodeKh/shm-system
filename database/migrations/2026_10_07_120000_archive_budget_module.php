<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Moves the budget-planning module's tables out of the app database (that module was removed from the code:
 * programs, activities, tasks, KPIs, PIP, BSP, PB, ceilings, credit movement, budget templates, items, units).
 *
 * The tables are moved, not deleted: into the database "<DB_DATABASE>_archive_20261007" when the server allows
 * creating it, otherwise renamed in place with the prefix "archive_20261007_". Their foreign keys are dropped
 * first (saved, so down() puts them back), and so are the rows of their create-table migrations, whose files
 * were removed. Fresh installs never create these tables, so there this does nothing.
 *
 * down() moves the tables back with their data, and the foreign keys between them. Keys from them to app tables
 * (entity, account, ...) are not put back: no migration drops these tables any more, so such a key would stop a
 * full rollback (migrate:refresh / migrate:reset) from dropping those app tables.
 * Once the server is confirmed, the archive can be dropped:
 *   DROP DATABASE `<DB_DATABASE>_archive_20261007`;
 */
return new class extends Migration
{
    private const TABLES = [
        'mef_activity',
        'mef_activity_expense',
        'mef_activity_to_entity',
        'mef_budget_template',
        'mef_budget_template_item_cost',
        'mef_budget_type',
        'mef_ceiling_column',
        'mef_ceiling_data',
        'mef_ceiling_entity',
        'mef_ceiling_exp_group',
        'mef_ceiling_exp_type',
        'mef_ceiling_rule',
        'mef_cluster_activity',
        'mef_cluster_activity_expense',
        'mef_cluster_activity_to_entity',
        'mef_costing_detail_to_entity',
        'mef_costing_to_entity',
        'mef_credit_movement',
        'mef_credit_movement_type',
        'mef_evaluate_kpi_subprogram',
        'mef_expenditure',
        'mef_expenditure_group',
        'mef_expenditure_imple_budget',
        'mef_expenditure_imple_budget_detail',
        'mef_expenditure_program',
        'mef_expenditure_program_detail',
        'mef_expenditure_type',
        'mef_finance_resource',
        'mef_finance_rule',
        'mef_income_group',
        'mef_income_type',
        'mef_item',
        'mef_item_category',
        'mef_item_subcategory',
        'mef_kpi_activity',
        'mef_kpi_cluster_activity',
        'mef_kpi_objective',
        'mef_kpi_program',
        'mef_kpi_subprogram',
        'mef_objective',
        'mef_pip_data_column',
        'mef_pip_grading',
        'mef_pip_inv_detail',
        'mef_pip_investment',
        'mef_pip_movement_year',
        'mef_planning_budget_group',
        'mef_price_chart',
        'mef_program',
        'mef_project',
        'mef_project_status',
        'mef_project_type',
        'mef_revenue',
        'mef_revenue_imple_budget',
        'mef_revenue_imple_budget_detail',
        'mef_revenue_imple_budget_to_acc_type',
        'mef_revenue_program',
        'mef_revenue_program_detail',
        'mef_staff_salary_to_position',
        'mef_subprogram',
        'mef_subprogram_expense',
        'mef_subprogram_to_entity',
        'mef_task',
        'unit',
    ];
    private const SUFFIX = '_archive_20261007';
    private const PREFIX = 'archive_20261007_';
    private const META = 'archive_20261007_restore';

    public function up(): void
    {
        $prefix = DB::getTablePrefix();
        $present = array_values(array_filter(self::TABLES, fn ($t) => $this->exists(DB::getDatabaseName(), $prefix.$t)));
        if (!$present) {
            echo "  archive: nothing to move\n";
            return;
        }

        $archive = $this->archiveDatabase();
        $meta = $archive ? "`{$archive}`.`".self::META.'`' : '`'.$prefix.self::META.'`';
        DB::statement("CREATE TABLE IF NOT EXISTS {$meta} (kind VARCHAR(20), payload JSON)");

        // Foreign keys of the moved tables (to each other and to app tables)
        $names = array_map(fn ($t) => $prefix.$t, $present);
        $in = implode(',', array_fill(0, count($names), '?'));
        $keys = DB::select("SELECT k.TABLE_NAME t, k.CONSTRAINT_NAME n, k.COLUMN_NAME c, k.REFERENCED_TABLE_NAME rt, k.REFERENCED_COLUMN_NAME rc, r.UPDATE_RULE u, r.DELETE_RULE d
            FROM information_schema.KEY_COLUMN_USAGE k JOIN information_schema.REFERENTIAL_CONSTRAINTS r
              ON r.CONSTRAINT_SCHEMA = k.TABLE_SCHEMA AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
            WHERE k.TABLE_SCHEMA = DATABASE() AND k.TABLE_NAME IN ({$in})", $names);
        foreach ($keys as $key) {
            DB::insert("INSERT INTO {$meta} VALUES ('foreign_key', ?)", [json_encode($key)]);
            DB::statement("ALTER TABLE `{$key->t}` DROP FOREIGN KEY `{$key->n}`");
        }

        // Their create-table migrations no longer exist
        $files = array_map(fn ($t) => '_create_'.$t.'_table', $present);
        $migrations = DB::table('migrations')->get()->filter(function ($row) use ($files) {
            foreach ($files as $end) if (str_ends_with($row->migration, $end)) return true;
            return false;
        });
        foreach ($migrations as $row) DB::insert("INSERT INTO {$meta} VALUES ('migration', ?)", [json_encode($row)]);
        DB::table('migrations')->whereIn('id', $migrations->pluck('id'))->delete();

        // One atomic move
        $moves = array_map(fn ($t) => '`'.$prefix.$t.'` TO '.$this->target($archive, $prefix.$t), $present);
        DB::statement('RENAME TABLE '.implode(', ', $moves));

        echo '  archive: moved '.count($present).' tables to '.($archive ? "database {$archive}" : 'the '.self::PREFIX.' prefix')
            .', dropped '.count($keys).' foreign keys, removed '.$migrations->count()." migration rows\n";
    }

    public function down(): void
    {
        $prefix = DB::getTablePrefix();
        $archive = DB::getDatabaseName().self::SUFFIX;
        $inArchiveDb = $this->exists($archive, self::META);
        if (!$inArchiveDb && !$this->exists(DB::getDatabaseName(), $prefix.self::META)) return;
        $meta = $inArchiveDb ? "`{$archive}`.`".self::META.'`' : '`'.$prefix.self::META.'`';

        $moves = [];
        foreach (self::TABLES as $t) {
            if ($inArchiveDb ? $this->exists($archive, $prefix.$t) : $this->exists(DB::getDatabaseName(), self::PREFIX.$prefix.$t)) {
                $from = $inArchiveDb ? "`{$archive}`.`{$prefix}{$t}`" : '`'.self::PREFIX.$prefix.$t.'`';
                $moves[] = "{$from} TO `{$prefix}{$t}`";
            }
        }
        if ($moves) DB::statement('RENAME TABLE '.implode(', ', $moves));

        $moved = array_map(fn ($t) => $prefix.$t, self::TABLES);
        foreach (DB::select("SELECT kind, payload FROM {$meta}") as $row) {
            $item = json_decode($row->payload);
            if ($row->kind === 'foreign_key') {
                if (!in_array($item->rt, $moved, true)) continue; // key to an app table: see above
                DB::statement("ALTER TABLE `{$item->t}` ADD CONSTRAINT `{$item->n}` FOREIGN KEY (`{$item->c}`) REFERENCES `{$item->rt}` (`{$item->rc}`) ON UPDATE {$item->u} ON DELETE {$item->d}");
            } elseif ($row->kind === 'migration' && !DB::table('migrations')->where('migration', $item->migration)->exists()) {
                DB::table('migrations')->insert(['migration' => $item->migration, 'batch' => $item->batch]);
            }
        }

        DB::statement("DROP TABLE {$meta}");
        if ($inArchiveDb && !DB::select('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? LIMIT 1', [$archive])) {
            DB::statement("DROP DATABASE `{$archive}`");
        }
    }

    // The archive database, or null when this server does not allow creating one
    private function archiveDatabase(): ?string
    {
        $name = DB::getDatabaseName().self::SUFFIX;
        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$name}`");
            return $name;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function target(?string $archive, string $table): string
    {
        return $archive ? "`{$archive}`.`{$table}`" : '`'.self::PREFIX.$table.'`';
    }

    private function exists(string $schema, string $table): bool
    {
        return (bool) DB::select('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [$schema, $table]);
    }
};
