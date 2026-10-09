<?php

namespace App\Services;

use App\Http\Controllers\LookupController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Form fields built from a table's columns, so a form follows the table without frontend changes.
 * Used by the settings pages (form_attributes in their list response) and the "+ Add" popups (/lookup/form/{table}).
 *
 * Each field: { name, type, required, default?, lookup? }. lookup: the choice list's name when it isn't the field name. Types:
 *   select   - the field has a choice list (LookupController), e.g. currency_id, status
 *   checkbox - yes / no (is_*, has_*, tinyint(1))
 *   text     - names, codes, symbols and other short text
 *   textarea - remark, description, address and long text columns
 *   number   - whole and decimal numbers, years
 *   date     - date and date-time columns
 *   file     - attach, file, photo, image, thumbnail
 * Order on every form: names, codes, choices, other inputs, numbers, order, yes / no, long text, files.
 */
class FormSchema
{
    // Filled in by the server, never typed in a form
    private const BOOKKEEPING = ['id', 'deleted', 'deleted_at', 'created_by', 'modified_by', 'updated_by', 'created_at', 'updated_at'];

    // Columns kept in the table but not edited in its settings form
    private const HIDDEN = [
        'currency' => ['rate'], // exchange rates live in currency_exchange
        'entity' => ['parent_id', 'deputy_commissionerofficer', 'professional_officer'], // set from the entity's own screens
    ];

    private const FILES = ['attach', 'file', 'photo', 'image', 'thumbnail'];

    /**
     * @param  string  $table  table name without prefix, e.g. "bank"
     * @param  bool  $withStatus  include the Active / Inactive status (a quick "+ Add" form leaves it to the server)
     */
    public static function fields(string $table, bool $withStatus = true): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $hidden = array_merge(self::BOOKKEEPING, self::HIDDEN[$table] ?? [], $withStatus ? [] : ['status']);
        $columns = collect(DB::select('show columns from '.DB::getTablePrefix().$table))
            ->reject(fn ($column) => in_array(strtolower($column->Field), $hidden, true));
        // The column a row is shown by: always required, so a choice never has an empty label
        $labelColumn = collect(['name_kh', 'name_en', 'name', 'title_kh', 'title_en', 'title'])
            ->first(fn ($name) => $columns->contains('Field', $name));

        $fields = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            $type = self::type($column);
            $field = [
                'name' => $name,
                'type' => $type,
                // Required when the database can't fill it in itself, and always for the label column
                'required' => $name === $labelColumn
                    || ($column->Null === 'NO' && $column->Default === null && $column->Extra === '' && $type !== 'checkbox'),
            ];
            if ($type === 'checkbox') {
                // Starts at the column's default; "active" starts on, so a new row can be used right away
                $field['default'] = $column->Default !== null ? (bool) $column->Default : $name === 'is_active';
            }
            if ($name === 'status') {
                // Active / Inactive. "status" means other things in other tables (request steps, paid / unpaid),
                // so this list goes by its own lookup name. A new row starts Active.
                $field['type'] = 'select';
                $field['lookup'] = 'record_status';
                $active = DB::table('mef_status')->where('group', 'STATUS')->where('title_en', 'Active')->value('id');
                if ($active) {
                    $field['default'] = $active;
                }
            }
            $fields[] = $field;
        }

        $rank = function ($field) {
            $order = ['name_kh', 'name_en', 'name', 'title_kh', 'title_en', 'title', 'code', 'abbreviation'];
            $at = array_search($field['name'], $order, true);
            if ($at !== false) {
                return $at;
            }
            if ($field['name'] === 'status') {
                return 65;
            }
            if ($field['name'] === 'order_level') {
                return 60;
            }

            return ['select' => 20, 'text' => 30, 'date' => 30, 'number' => 40, 'checkbox' => 70, 'textarea' => 80, 'file' => 90][$field['type']] ?? 50;
        };
        usort($fields, fn ($a, $b) => $rank($a) <=> $rank($b)); // stable: ties keep the table's order

        return $fields;
    }

    /** Form input type of one column (see the class comment) */
    public static function type(object $column): string
    {
        $name = strtolower($column->Field);
        $type = strtolower(preg_replace('/\(.+$/', '', $column->Type));

        if (in_array($name, self::FILES, true)) {
            return 'file';
        }
        if (LookupController::has($name)) {
            return 'select';
        }
        if (preg_match('/^(is|has)_/', $name) || str_starts_with(strtolower($column->Type), 'tinyint(1)')) {
            return 'checkbox';
        }
        if (preg_match('/^(remark|description|address|note)s?$/', $name)) {
            return 'textarea';
        }
        if (preg_match('/^(name|title|code|abbreviation|symbol)(_|$)/', $name)) {
            return 'text';
        }

        return match ($type) {
            'int', 'bigint', 'smallint', 'mediumint', 'tinyint', 'decimal', 'float', 'double', 'year' => 'number',
            'date', 'datetime', 'timestamp' => 'date',
            'text', 'mediumtext', 'longtext' => 'textarea',
            default => 'text',
        };
    }
}
