<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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
 * GET /api/v1/import/template?table= gives an empty workbook with the right headings.
 */
class ImportController extends Controller
{
    // Tables that have an import (the list's own /import endpoint does the actual import)
    private const TABLES = ['officer', 'officer_payable', 'officer_incentive', 'account', 'department', 'entity', 'payment_type'];

    // Filled in by the server, never read from the file
    private const SERVER_COLUMNS = ['created_by', 'modified_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    // Rows sent back for the table (all rows are still checked and counted)
    private const MAX_PREVIEW = 500;
    private const MAX_ROWS = 10000;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function preview(Request $request)
    {
        $this->useLocale($request);
        $request->validate([
            'table' => 'required|string',
            'file' => 'required|file|max:20480|mimes:xlsx,xls,csv,txt',
        ]);
        $table = $request->input('table');
        if ($denied = $this->refuse($table)) return $denied;

        try {
            $sheet = IOFactory::load($request->file('file')->getRealPath())->getSheet(0)->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            return response()->json(['message' => trans('import.unreadable')], 422);
        }
        $sheet = array_values(array_filter($sheet, fn ($row) => count(array_filter($row, fn ($v) => $v !== null && $v !== '')) > 0));
        if (count($sheet) < 2) {
            return response()->json(['message' => trans('import.empty')], 422);
        }

        $schema = $this->schema($table);
        // Headings as the import reads them (Laravel Excel's heading row: slugged with "_")
        $headings = array_map(fn ($h) => Str::slug((string) $h, '_'), array_shift($sheet));
        $columns = [];
        foreach ($headings as $i => $name) {
            if ($name === '') continue;
            $columns[$i] = [
                'name' => $name,
                'known' => isset($schema[$name]) && !in_array($name, self::SERVER_COLUMNS, true),
                'required' => $schema[$name]['required'] ?? false,
                'type' => $schema[$name]['type'] ?? null,
            ];
        }
        $present = array_column($columns, 'name');
        $missing = array_values(array_filter(array_keys($schema), fn ($name) => $schema[$name]['required'] && !in_array($name, $present, true)));

        $truncated = count($sheet) > self::MAX_ROWS;
        $sheet = array_slice($sheet, 0, self::MAX_ROWS);

        // Rows already in the table, by id, for new / update / same
        $ids = collect($sheet)->map(fn ($row) => $this->cell($row, $columns, 'id'))->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->unique()->values();
        $existing = $ids->isEmpty() || !isset($schema['id']) ? collect() : $ids->chunk(1000)
            ->flatMap(fn ($chunk) => DB::table($table)->whereIn('id', $chunk->all())->get())
            ->keyBy('id');

        $summary = ['total' => count($sheet), 'new' => 0, 'update' => 0, 'same' => 0, 'error' => 0];
        $rows = [];
        foreach ($sheet as $n => $raw) {
            $values = [];
            $errors = [];
            foreach ($columns as $i => $column) {
                $value = $raw[$i] ?? null;
                $value = is_string($value) ? trim($value) : $value;
                if ($column['known'] && $value !== null && $value !== '') {
                    [$value, $error] = $this->check($value, $column['type']);
                    if ($error) $errors[$column['name']] = $error;
                }
                $values[$column['name']] = $value === '' ? null : $value;
            }

            $id = $values['id'] ?? null;
            $current = is_numeric($id) ? $existing->get((int) $id) : null;
            if (!$current) {
                // A new row needs every required value
                foreach ($columns as $column) {
                    if ($column['required'] && $column['name'] !== 'id' && ($values[$column['name']] ?? null) === null) {
                        $errors[$column['name']] = trans('import.required');
                    }
                }
            }

            $changed = [];
            if ($current && !$errors) {
                foreach ($columns as $column) {
                    $name = $column['name'];
                    if (!$column['known'] || $name === 'id') continue;
                    if ((string) ($values[$name] ?? '') !== (string) ($current->$name ?? '')) $changed[] = $name;
                }
            }

            $status = $errors ? 'error' : ($current ? ($changed ? 'update' : 'same') : 'new');
            $summary[$status]++;
            if (count($rows) < self::MAX_PREVIEW || $status === 'error') {
                // Errors are always sent, so every one can be fixed from the preview
                $rows[] = ['line' => $n + 2, 'status' => $status, 'values' => $values, 'errors' => $errors, 'changed' => $changed];
            }
        }

        return response()->json([
            'columns' => array_values($columns),
            'missing' => $missing,
            'rows' => $rows,
            'summary' => $summary,
            'shown' => count($rows),
            'truncated' => $truncated,
            // The file can be imported only when nothing is wrong in it
            'ready' => !$missing && $summary['error'] === 0 && !$truncated,
        ]);
    }

    /** An empty workbook with the table's importable columns as headings (required ones first) */
    public function template(Request $request)
    {
        $table = (string) $request->query('table');
        if ($denied = $this->refuse($table)) return $denied;

        $schema = $this->schema($table);
        $names = collect($schema)->reject(fn ($c, $name) => in_array($name, self::SERVER_COLUMNS, true))
            ->sortByDesc(fn ($c, $name) => $name === 'id' ? 2 : (int) $c['required'])->keys()->all();

        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle(Str::limit($table, 28, ''));
        foreach ($names as $i => $name) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1).'1';
            $sheet->setCellValue($cell, $name);
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1))->setWidth(max(12, strlen($name) + 4));
        }
        $sheet->getStyle('A1:'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, count($names))).'1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        ob_start();
        (new Xlsx($book))->save('php://output');
        return response(ob_get_clean(), 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$table.'-template.xlsx"');
    }

    /** null when the user may import into $table, else the refusal */
    private function refuse(string $table)
    {
        if (!in_array($table, self::TABLES, true)) {
            return response()->json(['message' => trans('import.not_supported')], 404);
        }
        if (!CommonService::can('import_'.$table) && !CommonService::can('create_'.$table)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        return null;
    }

    /** Columns of the table: name => [type (number|date|text), required (must have a value in a new row)] */
    private function schema(string $table): array
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
                'required' => !$column['nullable'] && $column['default'] === null && !$column['auto_increment']
                    && !in_array($column['name'], self::SERVER_COLUMNS, true),
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
            if ($column['name'] === $name) return $row[$i] ?? null;
        }
        return null;
    }

    private function useLocale(Request $request): void
    {
        $locale = $request->header('X-Locale', 'kh');
        App::setLocale(in_array($locale, ['kh', 'en'], true) ? $locale : 'kh');
    }
}
