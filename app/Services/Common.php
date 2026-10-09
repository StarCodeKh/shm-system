<?php

namespace App\Services;

use App\Http\Controllers\LookupController;
use App\User;
use Auth;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Common
{
    /**
     * The signed-in user's permissions as [name => true] (empty array when none).
     * Cached for the request: controllers call this several times per action.
     */
    public static function permission($table = null)
    {
        static $cache = [];
        $user = Auth::user();
        if (! $user) {
            return [];
        }
        if (! array_key_exists($user->id, $cache)) {
            // Super Admin passes every check, even on permission names that were never created
            $cache[$user->id] = self::isSuperAdmin($user)
                ? new FullAccess(Permission::pluck('name')->all())
                : User::find($user->id)->getPermissionsViaRoles()
                    ->mapWithKeys(fn ($permission) => [$permission->name => true])
                    ->all();
        }

        return $cache[$user->id];
    }

    /** Whether the user (default: signed-in) has the Super Admin role from config/access.php */
    public static function isSuperAdmin($user = null)
    {
        $user = $user ?: Auth::user();
        $role = config('access.super_admin_role');

        return $user && $role && User::find($user->id)->hasRole($role);
    }

    /** Whether a role is the Super Admin role (its permissions and name are fixed) */
    public static function isSuperAdminRole($role)
    {
        return $role && $role->name === config('access.super_admin_role');
    }

    /** Whether the signed-in user has a permission (by name, or by a feature key in config/access.php) */
    public static function can($permission)
    {
        $name = config('access.'.$permission, $permission);

        return ! empty(self::permission()[$name]);
    }

    public static function getPermissionsViaRoles()
    {
        $user = User::find(Auth::user()->id);

        return $user->getPermissionsViaRoles();
    }

    public static function getPermissionsViaRolesByUser($roleId)
    {
        $assignPermissionData = '';
        $role = Role::findById($roleId);
        $assignPermissions = $role->permissions;
        foreach ($assignPermissions as $assignPermission) {
            $assignPermissionData .= $assignPermission->name.',';
        }
        $convertAssignPermission = explode(',', $assignPermissionData);

        $data = [];
        $permissions = Permission::get();
        $permission_data = [];
        foreach ($permissions as $permission) {
            if (in_array($permission->name, $convertAssignPermission)) {
                $data[$permission->name] = true;
            } else {
                $data[$permission->name] = false;
            }
        }

        return $data;
    }

    public static function getAllPermissionData()
    {
        $data = [];
        $query = DB::table('permission_group')->orderBy('name')->get();
        foreach ($query as $row) {
            $permissions = Permission::where('group', $row->id)->orderBy('name')->get();
            $permission_data = [];
            foreach ($permissions as $permission) {
                $permission_data[] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'permission' => $permission_data,
            ];
        }

        return $data;
    }

    public static function convertDataType($case)
    {
        // Fields with a shared choice list are dropdowns (see LookupController)
        if (LookupController::has(strtolower($case->Field))) {
            return 'select';
        }
        if ($case->Field == 'attach' || $case->Field == 'file' || $case->Field == 'photo' || $case->Field == 'image' || $case->Field == 'thumbnail') {
            $type = 'file';
        } elseif ($case->Field == 'request_title') {
            $type = 'text';
        } elseif (preg_match('/^(name|title)_(en|kh)$|^(fullname|latin|first_name|last_name|file_name|document_name|notifiable_type)$/', $case->Field)) {
            // Names and titles are typed in, whatever the column type (VARCHAR or TEXT)
            $type = 'text';
        } elseif (str_starts_with(strtolower($case->Type), 'tinyint(1)') || (str_starts_with($case->Field, 'is_') && $case->Field !== 'is_nssf_member')) {
            // Yes / no flags (MySQL reports boolean columns as tinyint(1))
            $type = 'checkbox';
        } else {
            switch ($case->Type) {
                case 'bigint':
                    $type = 'number';
                    break;
                case 'varchar(255)':
                    $type = 'select';
                    break;
                case 'varchar(50)':
                    $type = 'select';
                    break;
                case 'varchar(25)':
                    $type = 'hidden';
                    break;
                case 'datetime':
                    $type = 'date';
                    break;
                case 'date':
                    $type = 'date';
                    break;
                case 'float(20,4)':
                    $type = 'number';
                    break;
                case 'int':
                    $type = 'number';
                    break;
                case 'text':
                    $type = 'text';
                    break;
                case 'longtext':
                    $type = 'textarea';
                    break;
                case 'tinyint':
                    $type = 'checkbox';
                    break;
                case 'tinytext':
                    $type = 'checkbox';
                    break;
                case 'binary(255)':
                    $type = 'password';
                    break;
                case 'mediumtext':
                    $type = 'file';
                    break;
                default:
                    $type = 'text';
                    break;
            }
        }

        return $type;
    }

    public static function convertDataTypeWay1($case)
    {
        // Normalize field and type
        $field = strtolower($case->Field);
        $casetype = strtolower($case->Type);
        $type = preg_replace('/\(.+\)/', '', $casetype); // remove size/precision
        // 1. Special file fields
        $fileFields = ['attach', 'file', 'photo', 'image', 'thumbnail'];
        if (in_array($field, $fileFields)) {
            return 'file';
        }
        // 2. Select/lookup fields by convention (choices: the page's own list, else /api/v1/lookup/{field})
        if (LookupController::has($field)) {
            return 'select';
        }
        if (preg_match('/_id$/', $field) && $field != 'user_id' || preg_match('/_type$/', $field) || preg_match('/_code$/', $field) && $field != 'bank_code' || preg_match('/_method$/', $field) || preg_match('/status$/', $field) || preg_match('/department$/', $field) || preg_match('/title$/', $field) && $field != 'request_title' || preg_match('/bank$/', $field)) {
            return 'select';
        }

        // exceptional_case fields like request_title
        $excptFields = ['request_title', 'title_en', 'title_kh'];
        if (in_array($field, $excptFields)) {
            return 'text';
        }

        $fileFieldHiddens = ['user_id'];
        if (in_array($field, $fileFieldHiddens)) {
            return 'hidden';
        }
        // 3. Datatype mapping
        switch ($type) {
            case 'bigint':
            case 'int':
                return 'number'; // but _id handled above as select
            case 'varchar':
                return 'text';   // keep simple, unless _id/_type/_code
            case 'datetime':
            case 'timestamp':
            case 'date':
                return 'date';
            case 'float':
            case 'double':
            case 'decimal':
                return 'number';
            case 'text':
            case 'longtext':
            case 'mediumtext':
                return 'textarea';
            case 'tinyint':
                return 'checkbox'; // often boolean
            case 'binary':
            case 'varbinary':
                return 'password';
            default:
                return 'text'; // safe fallback
        }
    }

    protected static $customFieldTypes = [
        'expenses' => [
            'currency' => 'select',
            'status' => 'select',
        ],
    ];

    public static function getSingleOpt($model, $field_name)
    {
        $query = $model::orderByLevel()->whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => self::localName($row),
                'value' => $row->name_kh ?: $row->name_en, // saved on records: stays the same in every language
            ];
        }

        return $data;
    }

    public static function getLen($var)
    {
        $tmp = explode('.', $var);
        if (count($tmp) > 1) {
            return strlen($tmp[1]);
        }
    }

    public static function formatDoubleNum($num)
    {
        $n = number_format($num, Common::getLen($num));

        return $n;
    }

    public static function GetNumberOfMonth($startDate, $endDate)
    {
        $date1 = $startDate;
        $date2 = $endDate;
        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;

        return $diff;
    }

    public function convertToKhmerNumber($number)
    {
        return $data;
    }

    /**
     * Import the uploaded spreadsheet (request "file") with a Laravel Excel import class: one answer for every list.
     * The upload itself is passed, not its temp path, so the reader follows the file's extension (.xlsx, .xls, .csv).
     * Laravel Excel runs the import in one database transaction: when a row fails, nothing is saved.
     */
    public static function importSpreadsheet($import, $request)
    {
        $file = $request->file('file');
        if (! $file || ! $file->isValid()) {
            return response()->json(['success' => false, 'message' => trans('common.file_upload_failed')], 422);
        }
        try {
            Excel::import($import, $file);
        } catch (\Throwable $e) {
            report($e); // the details go to the log, the user gets a plain message

            return response()->json(['success' => false, 'message' => trans('import.failed')], 422);
        }

        return response()->json(['success' => true, 'message' => trans('common.file_uploaded')]);
    }

    /**
     * List filter from request input. Column names, sort direction and paging come from the
     * client and end up in SQL, so only plain column names are accepted, secrets can never be
     * searched (a LIKE on password would leak hashes one character at a time), and page size is capped.
     */
    public static function getFilter($obj)
    {
        $column = '/^[a-z][a-z0-9_]*$/i';
        $secret = '/pass|token|secret|remember/i';
        $searchFields = [];
        foreach ((array) ($obj['search_field'] ?? []) as $item) {
            if (! is_array($item)) {
                continue;
            }
            $item = array_filter($item, fn ($name) => is_string($name) && preg_match($column, $name) && ! preg_match($secret, $name), ARRAY_FILTER_USE_KEY);
            if ($item) {
                $searchFields[] = $item;
            }
        }
        $sort = isset($obj['sort']) && is_string($obj['sort']) && preg_match('/^[a-z][a-z0-9_.]*$/i', $obj['sort']) ? $obj['sort'] : setting('config_sort');
        $order = isset($obj['order']) && in_array(strtolower((string) $obj['order']), ['asc', 'desc'], true) ? $obj['order'] : setting('config_order');
        // Page size: a number (up to 1000) or "all" (every row, up to 5000)
        $limit = $obj['limit'] ?? null;
        $limit = $limit === 'all' ? 5000 : max(1, min((int) ($limit ?: setting('config_limit')), 1000));
        $filter = [
            'data' => $obj,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order,
            'page_number' => max(1, (int) ($obj['page_number'] ?? 1)),
            'search_field' => $searchFields ?: '',
        ];

        return $filter;
    }

    public static function queryTable($db_table, $filter, $flag)
    {
        $query = $db_table::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        if ($flag != 1 && $flag != 2) {
            $whereClause->limit($filter['limit']);
            $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        }
        if ($flag != 2) {
            if ($filter['search_field']) {
                $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
                $dataFields = $arraySingle;
                foreach ($dataFields as $key => $field) {
                    if (array_key_exists($key, $dataFields)) {
                        $whereClause->Where($key, $dataFields[$key]);
                    }
                }
            }
        }

        return $whereClause;
    }

    // Name in the request's language (X-Locale, see SetLocale): name_en in English, else name_kh; the other one when empty
    public static function localName($row, string $field = 'name'): string
    {
        $kh = data_get($row, $field.'_kh');
        $en = data_get($row, $field.'_en');

        return (string) (app()->getLocale() === 'en' ? ($en ?: $kh) : ($kh ?: $en));
    }

    public static function optVals($db_table, $filter, $flag)
    {
        $data = [];
        $whereClause = Common::queryTable($db_table, $filter, $flag); // Flag == 1 mean if query result limit by qty
        $queryResult = collect($whereClause->get());
        foreach ($queryResult as $row) {
            $data[] = [
                'label' => self::localName($row),
                'value' => $row->id,
            ];
        }

        return $data;
    }
}
