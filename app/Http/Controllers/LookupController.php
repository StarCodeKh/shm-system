<?php

namespace App\Http\Controllers;

use App\Models\Settings\Account;
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
use App\Models\Settings\Status;
use App\Models\Settings\Title;
use App\Services\FormSchema;
use CommonService;
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
            // Accounts: the parent account and the account type are stored by code
            'parent_code' => fn () => Account::query()->whereNotDeleted()->orderBy('code')->get()
                ->map(fn ($row) => ['label' => $row->code.' - '.CommonService::localName($row), 'value' => $row->code])->values()->all(),
            'account_type' => fn () => AccountType::query()->whereNotDeleted()->orderByLevel()->get()
                ->map(fn ($row) => ['label' => $row->type_code.' - '.CommonService::localName($row), 'value' => $row->type_code])->values()->all(),
            // Roles: which records the role may see (App\Services\DataScope)
            'data_scope' => fn () => collect(\App\Services\DataScope::LEVELS)
                ->map(fn ($level) => ['label' => trans('common.data_scope_'.$level), 'value' => $level])->all(),
            // Users: the member record a login belongs to (members see their own data with the "member" scope)
            'officer_id' => fn () => \App\Models\Officer::getSingleOpt(),
            // Active / Inactive of a settings row (its "status" field asks for this list by name, see FormSchema)
            'record_status' => fn () => Status::query()->where('group', 'STATUS')->whereNotDeleted()->orderByLevel()->get()
                ->map(fn ($row) => ['label' => CommonService::localName($row, 'title'), 'value' => $row->id])->values()->all(),
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

    /** A field's choice list, or null when it has none (the import preview checks values against it) */
    public static function choices(string $field): ?array
    {
        $lookup = (new static)->lookups()[$field] ?? null;
        return $lookup ? $lookup() : null;
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

        return response()->json(FormSchema::fields($table, false));
    }
}
