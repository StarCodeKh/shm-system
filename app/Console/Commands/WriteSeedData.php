<?php

namespace App\Console\Commands;

use Database\Seeders\SeedData;
use Database\Seeders\StatusSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Writes the seed files (database/seeders/data/{table}.php) from this database, in the import templates' columns:
 * set a server up in the app's Settings, then run this so new servers start with the same data.
 *
 *   php artisan db:seed-data                      (every table of SeedData::TABLES)
 *   php artisan db:seed-data --tables=title,position
 *
 * Deleted rows (status 4) are left out. Ids are only kept where the code relies on them (see SeedData::TABLES).
 */
class WriteSeedData extends Command
{
    protected $signature = 'db:seed-data
        {--tables= : Comma-separated tables (default: all seeded tables)}
        {--out= : Folder (default: database/seeders/data)}';

    protected $description = 'Write the seed data files from this database, in the import template columns';

    public function handle()
    {
        $tables = $this->option('tables') ? array_map('trim', explode(',', $this->option('tables'))) : array_keys(SeedData::TABLES);
        $out = $this->option('out') ? rtrim($this->option('out'), '/') : null;

        foreach ($tables as $table) {
            $keep = SeedData::TABLES[$table][0] ?? null;
            if (! $keep) {
                $this->error("{$table} is not a seeded table (SeedData::TABLES).");

                return 1;
            }
            $has = array_flip(Schema::getColumnListing($table));
            $query = DB::table($table);
            if (isset($has['status']) && $table !== 'mef_status') {
                $query->whereNotDeleted();
            }
            if (isset($has['order_level']) && $table !== 'mef_status') {
                $query->orderBy('order_level');
            }
            $rows = $query->orderBy('id')->get()->map(fn ($row) => (array) $row)->map(function ($row) use ($keep) {
                $fixed = $keep === 'all' || ($keep === 'fixed' && in_array($row['group'], StatusSeeder::FIXED_GROUPS, true));
                if (! $fixed) {
                    $row['id'] = null;
                }

                return $row;
            })->all();

            SeedData::write($table, $rows, $out ? "{$out}/{$table}.php" : null);
            $this->line(sprintf('  <info>ok</info>    %-22s %5d rows', "{$table}.php", count($rows)));
        }

        return 0;
    }
}
