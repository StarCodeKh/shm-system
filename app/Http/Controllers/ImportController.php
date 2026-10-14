<?php

namespace App\Http\Controllers;

use App\Models\Settings\Status;
use Carbon\Carbon;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Check an import file before it is imported: POST /api/v1/import/preview (file, table).
 *
 * The list imports (app/Imports) read row 1 as column names and match rows on "id": a new id is added,
 * a known one updated. This reads the file the same way, saves nothing, and reports per row:
 *   new | update (+ the fields that change) | same (nothing changes) | error (+ why)
 * and per column: known / ignored (not in the table) / required but missing from the file.
 * Cells can be fixed and rows removed in the preview (edits): the preview checks the file with them, and
 * POST /api/v1/import/file gives the fixed file back for the import.
 * GET /api/v1/import/template?table= gives an empty workbook with the right headings.
 */
class ImportController extends Controller
{
    // Tables that have an import (the list's own /import endpoint does the actual import)
    private const TABLES = [
        'officer', 'officer_payable', 'officer_payment', 'officer_incentive', 'account', 'department', 'entity', 'payment_type',
        // lists with App\Http\Controllers\Concerns\ImportsFromTemplate
        'title', 'position', 'bank', 'letter_type', 'account_type_group', 'account_type', 'account_group', 'mef_planning',
    ];

    // Filled in by the server, never read from the file
    private const SERVER_COLUMNS = ['created_by', 'modified_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    // Columns a table's import never takes from a file (shown as ignored, never compared)
    private const NOT_IMPORTED = [
        'officer' => ['user_id'], // the login is linked from the member's own screen
    ];

    // Columns that look like identifiers (*_no) but may repeat
    private const NOT_KEYS = [
        'officer_payment' => ['receipt_no'], // one receipt can pay several years
    ];

    // Rows sent back for the table (all rows are still checked and counted)
    private const MAX_PREVIEW = 500;

    private const MAX_ROWS = 10000;

    // Choice lists longer than this are still checked, but not offered as a dropdown
    private const MAX_OPTIONS = 300;

    // "status" is a row of mef_status, from the group(s) each table uses (same lists as its form)
    private const STATUS_GROUPS = [
        'officer' => ['STATUS', 'OFFICER'],
        'officer_payable' => ['PAYMENT'],
        'officer_payment' => ['PAYMENT'],
        'officer_incentive' => ['GERNAL'],
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function preview(Request $request)
    {
        $this->useLocale($request);
        $read = $this->read($request);
        if (! is_array($read)) {
            return $read;
        }
        [, $sheet, $columns, $schema] = $read;
        $table = $request->input('table');

        // Fixes made in the preview count as if they were in the file
        $edits = $this->edits($request, $sheet, $columns);
        foreach ($edits['cells'] as $line => $cells) {
            foreach ($cells as $i => $value) {
                $sheet[$line - 1][$i] = $value;
            }
        }
        foreach ($edits['removed'] as $line) {
            unset($sheet[$line - 1]);
        }

        // Id columns with a choice list (status, currency_id, ...): a value must be one of the choices
        $choices = $this->choices($table, $columns);
        // Other links to another table (the database's foreign keys): a value must be a row there
        $links = $this->links($table, $columns, $choices, $sheet);

        $present = array_column($columns, 'name');
        $missing = array_values(array_filter(array_keys($schema), fn ($name) => $schema[$name]['required'] && ! in_array($name, $present, true)));

        $truncated = count($sheet) > self::MAX_ROWS;
        $sheet = array_slice($sheet, 0, self::MAX_ROWS, true);

        // Rows already in the table, by id, for new / update / same
        $ids = collect($sheet)->map(fn ($row) => $this->cell($row, $columns, 'id'))->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->unique()->values();
        $existing = $ids->isEmpty() || ! isset($schema['id']) ? collect() : $ids->chunk(1000)
            ->flatMap(fn ($chunk) => DB::table($table)->whereIn('id', $chunk->all())->get())
            ->keyBy('id');

        // The same record twice: identifier columns (code, email, *_no, unique keys) and whole rows
        $keys = $this->keyColumns($table, $columns, $choices, $links);
        $seen = $this->firstLines($sheet, $columns, $keys);
        $taken = $this->taken($table, $schema, $sheet, $keys);

        $summary = ['total' => count($sheet), 'new' => 0, 'update' => 0, 'same' => 0, 'error' => 0];
        $rows = [];
        foreach ($sheet as $n => $raw) {
            $values = [];
            $errors = [];
            $suggest = [];
            foreach ($columns as $i => $column) {
                $value = $raw[$i] ?? null;
                $value = is_string($value) ? trim($value) : $value;
                if ($column['known'] && $value !== null && $value !== '' && isset($choices[$i])) {
                    // A choice's id is right; its name ("Active", "សកម្ម") is wrong but tells which id was meant
                    $match = $choices[$i]['aliases'][$this->normal($value)] ?? null;
                    if ($match === null || $this->normal($match) !== $this->normal($value)) {
                        $errors[$column['name']] = trans('import.not_in_list');
                        if ($match !== null) {
                            $suggest[$column['name']] = $match;
                        }
                    } else {
                        $value = $match;
                    }
                } elseif ($column['known'] && $value !== null && $value !== '') {
                    [$value, $error] = $this->check($value, $column['type']);
                    if (! $error && isset($links[$i]) && ! isset($links[$i]['found'][$this->normal($value)])) {
                        $error = trans('import.not_found', ['table' => $links[$i]['table']]);
                    }
                    if ($error) {
                        $errors[$column['name']] = $error;
                    }
                }
                $values[$column['name']] = $value === '' ? null : $value;
            }

            $id = $values['id'] ?? null;
            $current = is_numeric($id) ? $existing->get((int) $id) : null;
            if (! $current) {
                // A new row needs every required value
                foreach ($columns as $column) {
                    if ($column['required'] && $column['name'] !== 'id' && ($values[$column['name']] ?? null) === null) {
                        $errors[$column['name']] = trans('import.required');
                    }
                }
            }

            // A later row repeating an earlier one (same identifier, or the very same values)
            $duplicateOf = null;
            foreach ($keys as $i => $name) {
                $key = $this->normal($raw[$i] ?? '');
                $first = $key === '' ? null : ($seen['keys'][$i][$key] ?? null);
                if ($first !== null && $first !== $n + 1) {
                    $errors[$name] = trans('import.duplicate_row', ['line' => $first]);
                    $duplicateOf = $duplicateOf ?? $first;
                }
            }
            $signature = $this->signature($raw, $columns);
            $first = $signature === null ? null : ($seen['rows'][$signature] ?? null);
            if (! $duplicateOf && $first !== null && $first !== $n + 1) {
                $errors[$seen['label']] = trans('import.duplicate_row', ['line' => $first]);
                $duplicateOf = $first;
            }
            // An identifier another record already has: a new row would make a second copy of it
            // (a repeat inside the file is reported as that: removing it fixes both)
            foreach ($duplicateOf ? [] : $keys as $i => $name) {
                $other = $taken[$i][$this->normal($raw[$i] ?? '')] ?? null;
                if (! $other || ($current && (int) $current->id === (int) $other['id'])) {
                    continue;
                }
                $errors[$name] = trans('import.exists', ['record' => $other['label']]);
                // With an id column the row can update that record instead
                if (! $current && $this->hasColumn($columns, 'id')) {
                    $errors['id'] = trans('import.link', ['id' => $other['id']]);
                    $suggest['id'] = $other['id'];
                }
            }

            $changed = [];
            if ($current && ! $errors) {
                foreach ($columns as $column) {
                    $name = $column['name'];
                    if (! $column['known'] || $name === 'id') {
                        continue;
                    }
                    // Numbers as numbers: 2500000 in the file is the 2500000.0000 of a decimal column
                    $new = $values[$name] ?? '';
                    $old = $current->$name ?? '';
                    if (is_numeric($new) && is_numeric($old) ? $new + 0 != $old + 0 : (string) $new !== (string) $old) {
                        $changed[] = $name;
                    }
                }
            }

            $status = $errors ? 'error' : ($current ? ($changed ? 'update' : 'same') : 'new');
            $summary[$status]++;
            if (count($rows) < self::MAX_PREVIEW || $status === 'error') {
                // Errors are always sent, so every one can be fixed from the preview
                $rows[] = ['line' => $n + 1, 'status' => $status, 'values' => $values, 'errors' => $errors, 'changed' => $changed, 'suggest' => $suggest, 'duplicate_of' => $duplicateOf];
            }
        }

        return response()->json([
            // A column with a short choice list carries it, for a dropdown when fixing a cell
            'columns' => array_values(array_map(fn ($column, $i) => $column + [
                'options' => isset($choices[$i]) && count($choices[$i]['options']) <= self::MAX_OPTIONS ? $choices[$i]['options'] : null,
            ], $columns, array_keys($columns))),
            'missing' => $missing,
            'rows' => $rows,
            'summary' => $summary,
            'shown' => count($rows),
            'truncated' => $truncated,
            // The file can be imported only when nothing is wrong in it
            'ready' => ! $missing && $summary['error'] === 0 && ! $truncated,
        ]);
    }

    /**
     * The file with the preview's fixes in it (POST /api/v1/import/file: file, table, edits), as .xlsx.
     * The client sends this to the list's own /import, so every import reads the fixed values.
     */
    public function file(Request $request)
    {
        $this->useLocale($request);
        $read = $this->read($request);
        if (! is_array($read)) {
            return $read;
        }
        [$book, $rows, $columns] = $read;

        $edits = $this->edits($request, $rows, $columns);
        $sheet = $book->getSheet(0);
        foreach ($edits['cells'] as $line => $cells) {
            foreach ($cells as $i => $value) {
                $cell = Coordinate::stringFromColumnIndex($i + 1).$line;
                $number = str_replace([',', ' '], '', (string) $value);
                if ($value === null) {
                    $sheet->setCellValue($cell, null);
                } elseif ($columns[$i]['type'] === 'number' && is_numeric($number)) {
                    $sheet->setCellValueExplicit($cell, $number + 0, DataType::TYPE_NUMERIC);
                } elseif ($columns[$i]['type'] === 'date' && ! $this->check($value, 'date')[1]) {
                    // A real Excel date, as typed in Excel (some imports read dates as Excel day numbers)
                    $sheet->setCellValue($cell, ExcelDate::PHPToExcel(Carbon::parse($this->check($value, 'date')[0])));
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                } else {
                    // Text stays text (codes like "007" keep their zeros)
                    $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                }
            }
        }
        // Bottom first, so the rows above keep their numbers
        rsort($edits['removed']);
        foreach ($edits['removed'] as $line) {
            $sheet->removeRow($line);
        }
        // Only the first sheet is imported
        while ($book->getSheetCount() > 1) {
            $book->removeSheetByIndex(1);
        }

        ob_start();
        (new Xlsx($book))->save('php://output');

        return response(ob_get_clean(), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** An empty workbook with the table's importable columns as headings (required ones first) */
    public function template(Request $request)
    {
        $table = (string) $request->query('table');
        if ($denied = $this->refuse($table)) {
            return $denied;
        }

        $names = array_keys(self::templateColumns($table));

        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle(Str::limit($table, 28, ''));
        foreach ($names as $i => $name) {
            $cell = Coordinate::stringFromColumnIndex($i + 1).'1';
            $sheet->setCellValue($cell, $name);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth(max(12, strlen($name) + 4));
        }
        $sheet->getStyle('A1:'.Coordinate::stringFromColumnIndex(max(1, count($names))).'1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        ob_start();
        (new Xlsx($book))->save('php://output');

        return response(ob_get_clean(), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$table.'-template.xlsx"');
    }

    /**
     * The uploaded file, checked: [workbook, its non-empty rows keyed by row index (0 = Excel row 1, headings
     * taken out), columns keyed by column index, the table's schema], or the refusal / error response.
     */
    private function read(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'file' => 'required|file|max:20480|mimes:xlsx,xls,csv,txt',
            'edits' => 'nullable|json|max:2000000',
        ]);
        $table = $request->input('table');
        if ($denied = $this->refuse($table)) {
            return $denied;
        }

        try {
            $book = IOFactory::load($request->file('file')->getRealPath());
            $sheet = $book->getSheet(0)->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            return response()->json(['message' => trans('import.unreadable')], 422);
        }
        // Empty rows are skipped; keys are kept, so a row's line is its row number in Excel
        $sheet = array_filter($sheet, fn ($row) => count(array_filter($row, fn ($v) => $v !== null && $v !== '')) > 0);
        if (count($sheet) < 2) {
            return response()->json(['message' => trans('import.empty')], 422);
        }

        $schema = $this->schema($table);
        // Headings as the import reads them (Laravel Excel's heading row: slugged with "_")
        $headKey = array_key_first($sheet);
        $headings = array_map(fn ($h) => Str::slug((string) $h, '_'), $sheet[$headKey]);
        unset($sheet[$headKey]);
        $columns = [];
        foreach ($headings as $i => $name) {
            if ($name === '') {
                continue;
            }
            $columns[$i] = [
                'name' => $name,
                'known' => isset($schema[$name]) && ! in_array($name, self::SERVER_COLUMNS, true)
                    && ! in_array($name, self::NOT_IMPORTED[$table] ?? [], true),
                'required' => $schema[$name]['required'] ?? false,
                'type' => $schema[$name]['type'] ?? null,
            ];
        }

        return [$book, $sheet, $columns, $schema];
    }

    /**
     * Fixes made in the preview: {"cells": {"<line>": {"<column>": value}}, "removed": [<line>, ...]}, line = row number
     * in the file. Only known columns of the file's own rows are taken.
     * Returns ['cells' => [line => [column index => value or null]], 'removed' => [line, ...]].
     */
    private function edits(Request $request, array $rows, array $columns): array
    {
        $edits = json_decode((string) $request->input('edits', ''), true);
        $edits = is_array($edits) ? $edits : [];
        $index = [];
        foreach ($columns as $i => $column) {
            if ($column['known']) {
                $index[$column['name']] = $i;
            }
        }

        $cells = [];
        foreach ((array) ($edits['cells'] ?? []) as $line => $values) {
            $line = (int) $line;
            if (! isset($rows[$line - 1]) || ! is_array($values)) {
                continue;
            }
            foreach ($values as $name => $value) {
                if (! isset($index[$name]) || ! (is_scalar($value) || $value === null)) {
                    continue;
                }
                $value = $value === null ? '' : mb_substr(trim((string) $value), 0, 2000);
                $cells[$line][$index[$name]] = $value === '' ? null : $value;
            }
        }
        $removed = array_map('intval', array_filter((array) ($edits['removed'] ?? []), 'is_numeric'));
        $removed = array_values(array_unique(array_filter($removed, fn ($line) => isset($rows[$line - 1]))));

        return ['cells' => $cells, 'removed' => $removed];
    }

    /**
     * Choice lists of the file's id columns (number columns that have a dropdown in the forms), by column index:
     * ['options' => [{label, value}] in the user's language, 'aliases' => [normalized id or name => id]].
     * Names in both languages are aliases, so "Active" or "សកម្ម" in a file can be turned into its id.
     */
    private function choices(string $table, array $columns): array
    {
        $locale = App::getLocale();
        $choices = [];
        foreach ($columns as $i => $column) {
            if (! $column['known'] || $column['type'] !== 'number') {
                continue;
            }
            $name = $column['name'];
            if ($name !== 'status' && ! LookupController::has($name)) {
                continue;
            }

            $lists = [];
            foreach (array_unique([$locale, 'en', 'kh']) as $lang) {
                App::setLocale($lang);
                $lists[] = $name === 'status' ? $this->statuses($table) : (LookupController::choices($name) ?? []);
                // A long list (every officer) is checked by id only
                if (count($lists[0]) > self::MAX_OPTIONS) {
                    break;
                }
            }
            App::setLocale($locale);
            if (! $lists[0]) {
                continue;
            } // nothing to choose from yet: don't block the import

            $aliases = [];
            foreach (array_reverse($lists) as $list) {
                foreach ($list as $option) {
                    $aliases[$this->normal($option['label'])] = $option['value'];
                }
            }
            foreach ($lists[0] as $option) {
                $aliases[$this->normal($option['value'])] = $option['value'];
            }
            $choices[$i] = ['options' => $lists[0], 'aliases' => $aliases];
        }

        return $choices;
    }

    /**
     * Foreign-key columns of the file that have no choice list, by column index: ['table' => linked table,
     * 'found' => [normalized value => true] for the file's values that exist there]. One query per column.
     */
    private function links(string $table, array $columns, array $choices, array $rows): array
    {
        $prefix = DB::getTablePrefix();
        $links = [];
        foreach (Schema::getForeignKeys($table) as $key) {
            if (count($key['columns']) !== 1) {
                continue;
            }
            $i = collect($columns)->search(fn ($column) => $column['name'] === $key['columns'][0] && $column['known']);
            if ($i === false || isset($choices[$i])) {
                continue;
            }

            $linked = $prefix !== '' && str_starts_with($key['foreign_table'], $prefix) ? substr($key['foreign_table'], strlen($prefix)) : $key['foreign_table'];
            $target = $key['foreign_columns'][0];
            $values = collect($rows)->pluck($i)->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => is_string($v) ? trim($v) : $v)->unique()->values();
            $found = $values->chunk(1000)
                ->flatMap(fn ($chunk) => DB::table($linked)->whereIn($target, $chunk->all())->pluck($target))
                ->mapWithKeys(fn ($v) => [$this->normal($v) => true]);
            $links[$i] = ['table' => $linked, 'found' => $found->all()];
        }

        return $links;
    }

    /**
     * Columns that identify a record, by column index: the table's unique keys, else code / email / username / *_no
     * columns (identity_no, nssf_card_no, ...). Links to other tables (choices, foreign keys) are not identifiers.
     */
    private function keyColumns(string $table, array $columns, array $choices, array $links): array
    {
        $unique = collect(Schema::getIndexes($table))->filter(fn ($index) => $index['unique'] && ! $index['primary'] && count($index['columns']) === 1)
            ->map(fn ($index) => $index['columns'][0])->all();
        $keys = [];
        foreach ($columns as $i => $column) {
            if (! $column['known'] || $column['name'] === 'id' || isset($choices[$i]) || isset($links[$i])
                || in_array($column['name'], self::NOT_KEYS[$table] ?? [], true)) {
                continue;
            }
            if (in_array($column['name'], $unique, true) || preg_match('/^(code|email|username)$|_no$/', $column['name'])) {
                $keys[$i] = $column['name'];
            }
        }

        return $keys;
    }

    /** First line of each identifier value and of each whole row, so later repeats can point to it */
    private function firstLines(array $rows, array $columns, array $keys): array
    {
        $seen = ['keys' => [], 'rows' => [], 'label' => null];
        foreach ($rows as $n => $raw) {
            foreach ($keys as $i => $name) {
                $key = $this->normal($raw[$i] ?? '');
                if ($key !== '') {
                    $seen['keys'][$i][$key] ??= $n + 1;
                }
            }
            $signature = $this->signature($raw, $columns);
            if ($signature !== null) {
                $seen['rows'][$signature] ??= $n + 1;
            }
        }
        // A repeated row is marked on its first known column
        $seen['label'] = collect($columns)->first(fn ($column) => $column['known'] && $column['name'] !== 'id')['name'] ?? 'id';

        return $seen;
    }

    /**
     * A row's known values, for spotting the very same row twice; null when that would say little: a row with
     * an id (two ids are two records), or too few values to tell rows apart (a file with mostly ignored columns
     * would otherwise make every row with the same status a "repeat").
     */
    private function signature(array $raw, array $columns): ?string
    {
        $values = [];
        foreach ($columns as $i => $column) {
            if (! $column['known']) {
                continue;
            }
            $value = $this->normal($raw[$i] ?? '');
            if ($column['name'] === 'id') {
                if ($value !== '') {
                    return null;
                }

                continue;
            }
            $values[] = $value;
        }
        if (count($values) < 3 || count(array_filter($values, fn ($v) => $v !== '')) < 2) {
            return null;
        }

        return md5(json_encode($values));
    }

    /**
     * Records that already have the file's identifier values, by column index: [normalized value => ['id', 'label']].
     * Deleted records don't count. One query per identifier column.
     */
    private function taken(string $table, array $schema, array $rows, array $keys): array
    {
        if (! isset($schema['id'])) {
            return [];
        }
        $taken = [];
        foreach ($keys as $i => $name) {
            $values = collect($rows)->pluck($i)->map(fn ($v) => is_string($v) ? trim($v) : $v)->filter(fn ($v) => $v !== null && $v !== '')->unique()->values();
            foreach ($values->chunk(1000) as $chunk) {
                $query = DB::table($table)->whereIn($name, $chunk->all());
                if (isset($schema['status'])) {
                    $query->whereNotDeleted();
                }
                foreach ($query->get() as $record) {
                    $taken[$i][$this->normal($record->$name)] ??= ['id' => $record->id, 'label' => $this->recordLabel($record)];
                }
            }
        }

        return $taken;
    }

    /** "#12 Sok Dara": a record's id and its name, as far as the table has one */
    private function recordLabel(object $record): string
    {
        $kh = App::getLocale() === 'kh';
        $names = $kh ? ['name_kh', 'title_kh', 'name_en', 'title_en', 'name'] : ['name_en', 'title_en', 'name_kh', 'title_kh', 'name'];
        $name = collect($names)->map(fn ($field) => $record->$field ?? null)->first(fn ($v) => is_string($v) && trim($v) !== '');
        $person = trim(($record->last_name ?? '').' '.($record->first_name ?? ''));

        return trim('#'.$record->id.' '.($name ?: $person));
    }

    private function hasColumn(array $columns, string $name): bool
    {
        return collect($columns)->contains(fn ($column) => $column['name'] === $name);
    }

    /** The statuses a table's rows can have, labelled in the current language */
    private function statuses(string $table): array
    {
        $kh = App::getLocale() === 'kh';

        return DB::table('mef_status')->whereIn('group', self::STATUS_GROUPS[$table] ?? ['STATUS'])->where('id', '!=', Status::DELETED)
            ->orderBy('id')->get()
            ->map(fn ($row) => ['label' => ($kh ? ($row->title_kh ?: $row->title_en) : ($row->title_en ?: $row->title_kh)) ?: (string) $row->id, 'value' => $row->id])
            ->values()->all();
    }

    /** A value as compared with choices: text, trimmed, lower case, single spaces ("13.0" and 13 are the same id) */
    private function normal($value): string
    {
        if (is_float($value) && floor($value) == $value) {
            $value = (int) $value;
        }

        return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $value)));
    }

    /** null when the user may import into $table, else the refusal */
    private function refuse(string $table)
    {
        if (! in_array($table, self::TABLES, true)) {
            return response()->json(['message' => trans('import.not_supported')], 404);
        }
        if (! CommonService::can('import_'.$table) && ! CommonService::can('create_'.$table)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }

        return null;
    }

    /** Columns of the table: name => [type (number|date|text), required (must have a value in a new row)] */
    /** The template's columns, in its order (id, then the required ones): name => ['type', 'required'] */
    public static function templateColumns(string $table): array
    {
        return collect(self::schema($table))->reject(fn ($c, $name) => in_array($name, self::SERVER_COLUMNS, true))
            ->sortByDesc(fn ($c, $name) => $name === 'id' ? 2 : (int) $c['required'])->all();
    }

    private static function schema(string $table): array
    {
        $schema = [];
        foreach (Schema::getColumns($table) as $column) {
            $type = strtolower($column['type_name']);
            $schema[$column['name']] = [
                'type' => match (true) {
                    in_array($type, ['int', 'integer', 'bigint', 'smallint', 'mediumint', 'tinyint', 'decimal', 'float', 'double'], true) => 'number',
                    in_array($type, ['date', 'datetime', 'timestamp'], true) => 'date',
                    default => 'text',
                },
                'required' => ! $column['nullable'] && $column['default'] === null && ! $column['auto_increment']
                    && ! in_array($column['name'], self::SERVER_COLUMNS, true),
            ];
        }

        return $schema;
    }

    /** [the value as it will be saved, error or null] */
    private function check($value, ?string $type): array
    {
        if ($type === 'number') {
            $number = str_replace([',', ' '], '', (string) $value);

            return is_numeric($number) ? [$number + 0, null] : [$value, trans('import.not_number')];
        }
        if ($type === 'date') {
            try {
                // Excel stores dates as day numbers; text dates are read as written
                $date = is_numeric($value) ? Carbon::instance(ExcelDate::excelToDateTimeObject($value)) : Carbon::parse((string) $value);

                return [$date->format('Y-m-d'), null];
            } catch (\Throwable $e) {
                return [$value, trans('import.not_date')];
            }
        }

        return [$value, null];
    }

    private function cell(array $row, array $columns, string $name)
    {
        foreach ($columns as $i => $column) {
            if ($column['name'] === $name) {
                return $row[$i] ?? null;
            }
        }

        return null;
    }

    private function useLocale(Request $request): void
    {
        $locale = $request->header('X-Locale', 'kh');
        App::setLocale(in_array($locale, ['kh', 'en'], true) ? $locale : 'kh');
    }
}
