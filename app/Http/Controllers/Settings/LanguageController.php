<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Language;
use App\Models\Settings\Status;
use App\Services\FormSchema;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Settings › Language: the languages the system can be shown in.
 * GET /api/v1/languages (public, also before sign-in) gives the active ones to the language switch.
 */
class LanguageController extends Controller
{
    protected $PERMISSION_ACTION_LIST = 'list_language';

    protected $PERMISSION_ACTION_CREATE = 'create_language';

    protected $PERMISSION_ACTION_SHOW = 'show_language';

    protected $PERMISSION_ACTION_UPDATE = 'update_language';

    protected $PERMISSION_ACTION_DELETE = 'delete_language';

    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth')->except('active');
        $this->db_table = new Language;
    }

    private function can(string $permission): bool
    {
        return (CommonService::permission($this->db_table->getTable())[$permission] ?? false) == true;
    }

    private function denied()
    {
        return response()->json(['success' => false, 'is_display' => false, 'message' => trans('common.msg_no_permission_access')], 500);
    }

    /** Active languages for the language switch, default first */
    public function active()
    {
        return response()->json(['data' => Language::active()]);
    }

    public function index(Request $request)
    {
        if (! $this->can($this->PERMISSION_ACTION_LIST)) {
            return $this->denied();
        }
        $filter = CommonService::getFilter($request->all());

        return response()->json([
            'data' => Language::getData($filter),
            'limit' => $filter['limit'],
            'total' => Language::getTotal($filter),
            'permissions' => CommonService::permission($this->db_table),
            'role_permissions' => CommonService::getPermissionsViaRoles(),
            'model_name' => $this->db_table->getTable(),
            'data_fields' => Language::dataFields(),
            'form_attributes' => FormSchema::fields($this->db_table->getTable()),
            'is_display' => true,
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->can($this->PERMISSION_ACTION_CREATE)) {
            return $this->denied();
        }
        $row = Language::create($this->dataForm($request) + ['created_by' => Auth::user()->id]);

        return response()->json(['success' => true, 'message' => trans('common.msg_save_successfully'), 'data' => ['data' => $row]]);
    }

    public function show($id)
    {
        if (! $this->can($this->PERMISSION_ACTION_SHOW)) {
            return $this->denied();
        }

        return response()->json(['success' => true, 'message' => trans('common.msg_successfully'), 'data' => ['data' => Language::findOrFail($id)]]);
    }

    public function update(Request $request, $id)
    {
        if (! $this->can($this->PERMISSION_ACTION_UPDATE)) {
            return $this->denied();
        }
        $row = Language::findOrFail($id);
        $fields = $this->dataForm($request, $row);
        if (array_key_exists('status', $fields) && (int) $fields['status'] !== Status::ACTIVE && $this->isLastActive($row)) {
            return $this->lastActive();
        }
        $row->update($fields + ['modified_by' => Auth::user()->id]);

        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $row->fresh()]);
    }

    public function destroy($id)
    {
        if (! $this->can($this->PERMISSION_ACTION_DELETE)) {
            return $this->denied();
        }
        $row = Language::findOrFail($id);
        if ($this->isLastActive($row)) {
            return $this->lastActive();
        }
        $row->update(['status' => Status::DELETED]);

        return response()->json(['success' => true, 'message' => trans('common.msg_delete_successfully')]);
    }

    /** Form fields, validated. Forms send a list of single-field objects [{ name: … }, { code: … }] */
    private function dataForm(Request $request, ?Language $row = null): array
    {
        $input = $request->all();
        if (array_is_list($input)) {
            $input = array_merge(...array_filter($input, 'is_array') ?: [[]]);
        }
        if (isset($input['code'])) {
            $input['code'] = strtolower(trim((string) $input['code']));
        }
        $fields = validator($input, [
            'name' => [$row ? 'sometimes' : 'required', 'string', 'max:32'],
            // Two or three letters (kh, en, zh), once among the languages in use
            'code' => [$row ? 'sometimes' : 'required', 'regex:/^[a-z]{2,3}$/',
                Rule::unique('language', 'code')->ignore($row?->id)->where(fn ($q) => $q->where('status', '!=', Status::DELETED))],
            'image' => ['nullable', 'string', 'max:64'],
            'order_level' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer'],
        ])->validate();
        $fields['order_level'] = (int) ($fields['order_level'] ?? $row?->order_level ?? 0);

        return $fields;
    }

    private function isLastActive(Language $row): bool
    {
        return (int) $row->status === Status::ACTIVE
            && Language::where('status', Status::ACTIVE)->where('id', '!=', $row->id)->doesntExist();
    }

    private function lastActive()
    {
        return response()->json(['success' => false, 'message' => trans('common.language_keep_one_active')], 422);
    }
}
