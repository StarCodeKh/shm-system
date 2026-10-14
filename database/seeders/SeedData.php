<?php

namespace Database\Seeders;

use App\Http\Controllers\ImportController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Starting data of a new server: database/seeders/data/{table}.php, one row per record, with the import
 * template's columns as keys (as GET /api/v1/import/template gives them; empty columns are left out).
 * php artisan db:seed-data writes the files from a database, e.g. after setting a server up in the app.
 *
 * Seeders only add missing rows (matched on a natural key), so they are safe on a live server too.
 */
class SeedData
{
    // table => [ids, key, what it is]
    //   ids: which rows keep their id in the file. all: every row (a server seeded from the files then has those
    //        ids, so importing a list there shows "same"); fixed: only the statuses the code uses by id
    //        (StatusSeeder::FIXED_GROUPS), options get a free id
    //   key: the columns a row is matched on (never the id: an id already taken is left to the database)
    // Each list can still be changed later in the app, or uploaded from its Import screen.
    const TABLES = [
        'mef_status' => ['fixed', ['group', 'title_en'], 'Statuses (fixed ids, used by the code) and dropdown options'],
        'currency' => ['all', ['abbreviation'], 'Currencies: KHR (the default) and USD'],
        'config' => ['all', ['code', 'key'], 'Site settings; secrets are left empty, set them on each server'],
        'entity' => ['all', ['code'], 'General departments (old SHM business units): officer.entity_id'],
        'department' => ['all', ['code'], 'Their sections (old SHM operating units): officer.department_id'],
        'payment_type' => ['all', ['name_kh'], 'Benefits paid to members, in riel'],
        'payment_term' => ['all', ['type'], 'Contribution schedules; the default one is given to each new member'],
        'payment_method' => ['all', ['name_kh'], 'How a member pays'],
        'notification_type' => ['all', ['notification_type'], 'Types a notification is created with'],
        'taxable_salary' => ['all', ['taxable_salary_monthly_from'], 'Salary tax brackets incentives are taxed with'],
        'title' => ['all', ['name_kh'], 'ងារ (titles of the member form)'],
        'position' => ['all', ['name_kh'], 'តំណែង (positions)'],
        'bank' => ['all', ['name_kh'], 'Banks of the member\'s bank account'],
        'letter_type' => ['all', ['name_kh'], 'Letter types of incentives and requests'],
    ];

    public static function path(string $table): string
    {
        return __DIR__."/data/{$table}.php";
    }

    /** The file's rows as column => value */
    public static function rows(string $table): array
    {
        $path = self::path($table);
        if (! is_file($path)) {
            throw new \RuntimeException("Seed file missing: {$path} (php artisan db:seed-data writes it from a database).");
        }

        return require $path;
    }

    /** Adds the missing rows of a table's file, matched on its key (TABLES) */
    public static function seed(string $table, ?Command $command = null, ?array $rows = null): int
    {
        return self::addMissing($table, $rows ?? self::rows($table), self::TABLES[$table][1], $command);
    }

    /**
     * Writes a table's file: one line per row, keys in the template's column order, empty columns left out.
     */
    public static function write(string $table, array $rows, ?string $path = null): void
    {
        $columns = ImportController::templateColumns($table);
        $lines = [];
        foreach ($rows as $row) {
            $pairs = [];
            foreach ($columns as $column => ['type' => $type]) {
                $value = $row[$column] ?? null;
                if ($value !== null && $value !== '') {
                    $pairs[] = "'{$column}' => ".self::literal($value, $type === 'number');
                }
            }
            $lines[] = '    ['.implode(', ', $pairs).'],';
        }
        $path ??= self::path($table);
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, implode("\n", [
            '<?php',
            '',
            '// tbl_'.$table.': '.self::TABLES[$table][2].'.',
            '// Columns as in its import template. Matched on '.implode(' + ', self::TABLES[$table][1]).'; php artisan db:seed-data --tables='.$table.' writes this file.',
            'return [',
            ...$lines,
            '];',
            '',
        ]));
    }

    /**
     * Adds the rows whose $key (column names) is not in the table yet; rows already there are never changed.
     * Columns the table doesn't have are ignored. An id in the file is kept when it is free.
     * A link (foreign key) to a row that doesn't exist is left empty and reported.
     *
     * @return int rows added
     */
    public static function addMissing(string $table, array $rows, array $key, ?Command $command = null): int
    {
        $columns = array_flip(Schema::getColumnListing($table));
        $seen = DB::table($table)->get($key)->mapWithKeys(fn ($row) => [self::keyOf((array) $row, $key) => true])->all();
        $links = collect(Schema::getForeignKeys($table))->filter(fn ($fk) => count($fk['columns']) === 1)
            ->mapWithKeys(fn ($fk) => [$fk['columns'][0] => [self::plain($fk['foreign_table']), $fk['foreign_columns'][0]]])->all();
        $found = [];
        $now = now();
        $added = 0;

        foreach ($rows as $row) {
            $row = array_intersect_key($row, $columns);
            $id = self::keyOf($row, $key);
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;

            foreach ($links as $column => [$linked, $target]) {
                $value = $row[$column] ?? null;
                if ($value === null) {
                    continue;
                }
                $found[$column] ??= DB::table($linked)->pluck($target)->map(fn ($v) => (string) $v)->flip()->all();
                if (! isset($found[$column][(string) $value])) {
                    $command?->warn("  {$table}: {$column} {$value} is not in {$linked}, left empty (".implode(' / ', array_map(fn ($k) => $row[$k] ?? '', $key)).').');
                    $row[$column] = null;
                }
            }
            if (isset($row['id']) && DB::table($table)->where('id', $row['id'])->exists()) {
                unset($row['id']);
            }
            foreach (['created_at', 'updated_at'] as $stamp) {
                if (isset($columns[$stamp])) {
                    $row[$stamp] = $now;
                }
            }
            DB::table($table)->insert($row);
            $added++;
        }

        return $added;
    }

    /** A row's key as text; numbers as numbers, so 0 in a file is the 0.0000 of a decimal column */
    private static function keyOf(array $row, array $key): string
    {
        return implode("\u{1F}", array_map(function ($column) use ($row) {
            $value = $row[$column] ?? '';

            return is_numeric($value) ? (string) ($value + 0) : mb_strtolower(trim((string) $value));
        }, $key));
    }

    /** A value as PHP source: a number column's value bare (whole numbers without .0000), text in single quotes */
    private static function literal($value, bool $number): string
    {
        if ($number && is_numeric($value)) {
            $value += 0;

            return is_float($value) && floor($value) === $value ? (string) (int) $value : (string) $value;
        }

        return "'".str_replace(['\\', "'"], ['\\\\', "\\'"], (string) $value)."'";
    }

    private static function plain(string $table): string
    {
        $prefix = DB::getTablePrefix();

        return $prefix !== '' && str_starts_with($table, $prefix) ? substr($table, strlen($prefix)) : $table;
    }
}
