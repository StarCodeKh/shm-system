<?php

namespace App\Http\Controllers;

use App\Models\Settings\AccountGroup;
use App\Models\Settings\AccountType;
use App\Models\Settings\AccountTypeGroup;
use App\Models\Settings\Bank;
use App\Models\Settings\Currency;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\LetterType;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\PaymentType;
use App\Models\Settings\RoleUser;
use App\Models\Settings\Title;
use CommonService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Choice lists for form dropdowns: GET /api/v1/lookup/{field}.
 *
 * Forms build their fields from the table columns; a dropdown whose page didn't send its choices
 * asks here by field name. To give another field a dropdown everywhere, add it to lookups().
 * Each list is [{ label, value }], the same shape and value (id, code or name) the pages already send:
 * a form reloads a list from here after "+ Add" creates a new choice (see resources/js/src/quickAdd.js).
 */
class LookupController extends Controller
{
    protected function lookups(): array
    {
        return [
            // Columns that store the currency itself as text ("Reil", "Dollar"): value is that text
            'currency' => fn () => Currency::whereNotDeleted()->get()
                ->map(fn ($currency) => [
                    'label' => $currency->label(),
                    'value' => $currency->abbreviation ?: $currency->name_en,
                ])->values()->all(),
            // Columns that link to the currency table
            'currency_id' => fn () => Currency::getSingleOpt(),
            'from_currency' => fn () => Currency::getSingleOpt(),
            'to_currency' => fn () => Currency::getSingleOpt(),
            // Officer columns that store the bank's English name
            'bank' => fn () => Bank::getBankByName(),
            'bank_id' => fn () => $this->byId(Bank::class),
            'title' => fn () => Title::getSingleOpt(),
            'department_id' => fn () => Department::getSingleOpt(),
            'entity_id' => fn () => Entity::getSingleOpt(),
            'role_id' => fn () => RoleUser::getSingleOpt(),
            // Users store their role by name (Spatie role)
            'role' => fn () => RoleUser::getSingleValueOpt(),
            'payment_method_id' => fn () => PaymentMethod::getSingleOpt(),
            'payment_type_id' => fn () => $this->byId(PaymentType::class),
            'account_type_group_id' => fn () => $this->byId(AccountTypeGroup::class),
            'account_type_id' => fn () => $this->byId(AccountType::class),
            'account_group_id' => fn () => $this->byId(AccountGroup::class),
            'letter_type_id' => fn () => $this->byId(LetterType::class),
        ];
    }

    /** Rows of a name_kh / name_en table keyed by id, labelled in the user's language */
    protected function byId(string $model): array
    {
        $kh = app()->getLocale() === 'kh';
        return $model::query()->whereNotDeleted()->orderByLevel()->get()
            ->map(fn ($row) => [
                'label' => ($kh ? ($row->name_kh ?: $row->name_en) : ($row->name_en ?: $row->name_kh)) ?: (string) $row->id,
                'value' => $row->id,
            ])->values()->all();
    }

    /** Whether a field has a choice list here (forms then show it as a dropdown) */
    public static function has(string $field): bool
    {
        return array_key_exists($field, (new static)->lookups());
    }

    public function show(string $field)
    {
        $lookup = $this->lookups()[$field] ?? null;

        return $lookup
            ? response()->json($lookup())
            : response()->json(['message' => trans('common.lookup_no_choices')], 404);
    }

    /**
     * The "+ Add" form for a table: GET /api/v1/lookup/form/{table}.
     * Built from the table's columns, so it follows the table without frontend changes.
     * Only for users who may create rows there; bookkeeping columns are filled by the server and left out.
     */
    public function form(string $table)
    {
        if (!Schema::hasTable($table)) {
            return response()->json(['message' => trans('common.lookup_no_form')], 404);
        }
        // Same rule as saving (EnforceModulePermission): a create permission that exists is required
        $permission = 'create_'.$table;
        $declared = app(PermissionRegistrar::class)->getPermissions()->contains('name', $permission);
        if ($declared && !CommonService::can($permission)) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }

        $skip = ['id', 'status', 'deleted', 'deleted_at', 'created_by', 'modified_by', 'updated_by', 'created_at', 'updated_at'];
        $columns = collect(DB::select('show columns from '.DB::getTablePrefix().$table))
            ->reject(fn ($column) => in_array(strtolower($column->Field), $skip));
        // The column a choice is shown by: always asked for, so a new choice never has an empty label
        $labelColumn = collect(['name_kh', 'name_en', 'name', 'title_kh', 'title_en', 'title'])
            ->first(fn ($name) => $columns->contains('Field', $name));

        $fields = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            $type = CommonService::convertDataTypeWay1($column);
            if (preg_match('/^(is|has)_/', $name)) $type = 'checkbox';
            // Names and codes are one line, even when the column is a long text type
            elseif (preg_match('/^(name|title|code|abbreviation)(_kh|_en)?$/', $name)) $type = 'text';
            if ($type === 'hidden') continue;
            $field = [
                'name' => $name,
                'type' => $type,
                // Required when the database can't fill it in itself, and always for the label column
                'required' => $name === $labelColumn
                    || ($column->Null === 'NO' && $column->Default === null && $column->Extra === '' && $type !== 'checkbox'),
            ];
            if ($type === 'checkbox') {
                // Yes/no starts at the column's default; "active" starts on, so the new choice can be used
                $field['default'] = $column->Default !== null ? (bool) $column->Default : $name === 'is_active';
            }
            $fields[] = $field;
        }

        // Same order on every add form: names, code, choices, other inputs, numbers, yes/no, long text
        $rank = function ($field) {
            $order = ['name_kh', 'name_en', 'name', 'title_kh', 'title_en', 'title', 'code', 'abbreviation'];
            $at = array_search($field['name'], $order);
            if ($at !== false) return $at;
            if ($field['name'] === 'order_level') return 60;
            return ['select' => 20, 'text' => 30, 'date' => 30, 'number' => 40, 'checkbox' => 70, 'textarea' => 80, 'file' => 90][$field['type']] ?? 50;
        };
        usort($fields, fn ($a, $b) => $rank($a) <=> $rank($b)); // stable: ties keep the table's order

        return response()->json($fields);
    }
}
