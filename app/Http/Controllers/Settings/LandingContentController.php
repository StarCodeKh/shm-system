<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\LandingContent;
use App\Models\Settings\Language;
use App\Models\Settings\Status;
use App\Services\FormSchema;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * Settings › Landing page: the blocks of the public landing page.
 * GET /api/v1/landing (public, before sign-in) gives the page in the reader's language (X-Locale).
 */
class LandingContentController extends Controller
{
    protected $PERMISSION_ACTION_LIST = 'list_landing_content';

    protected $PERMISSION_ACTION_CREATE = 'create_landing_content';

    protected $PERMISSION_ACTION_SHOW = 'show_landing_content';

    protected $PERMISSION_ACTION_UPDATE = 'update_landing_content';

    protected $PERMISSION_ACTION_DELETE = 'delete_landing_content';

    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth')->except('page');
        $this->db_table = new LandingContent;
    }

    private function can(string $permission): bool
    {
        return (CommonService::permission($this->db_table->getTable())[$permission] ?? false) == true;
    }

    private function denied()
    {
        return response()->json(['success' => false, 'is_display' => false, 'message' => trans('common.msg_no_permission_access')], 500);
    }

    /** The landing page in the reader's language */
    public function page()
    {
        return response()->json(['data' => LandingContent::page(app()->getLocale())]);
    }

    public function index(Request $request)
    {
        if (! $this->can($this->PERMISSION_ACTION_LIST)) {
            return $this->denied();
        }
        $filter = CommonService::getFilter($request->all());

        return response()->json([
            'data' => LandingContent::getData($filter),
            'limit' => $filter['limit'],
            'total' => LandingContent::getTotal($filter),
            'permissions' => CommonService::permission($this->db_table),
            'role_permissions' => CommonService::getPermissionsViaRoles(),
            'model_name' => $this->db_table->getTable(),
            'data_fields' => Schema::getColumnListing($this->db_table->getTable()),
            'form_attributes' => FormSchema::fields($this->db_table->getTable()),
            'is_display' => true,
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->can($this->PERMISSION_ACTION_CREATE)) {
            return $this->denied();
        }
        $row = LandingContent::create($this->dataForm($request) + ['created_by' => Auth::user()->id]);

        return response()->json(['success' => true, 'message' => trans('common.msg_save_successfully'), 'data' => ['data' => $row]]);
    }

    public function show($id)
    {
        if (! $this->can($this->PERMISSION_ACTION_SHOW)) {
            return $this->denied();
        }

        return response()->json(['success' => true, 'message' => trans('common.msg_successfully'), 'data' => ['data' => LandingContent::findOrFail($id)]]);
    }

    public function update(Request $request, $id)
    {
        if (! $this->can($this->PERMISSION_ACTION_UPDATE)) {
            return $this->denied();
        }
        $row = LandingContent::findOrFail($id);
        $row->update($this->dataForm($request, $row) + ['modified_by' => Auth::user()->id]);

        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $row->fresh()]);
    }

    public function destroy($id)
    {
        if (! $this->can($this->PERMISSION_ACTION_DELETE)) {
            return $this->denied();
        }
        LandingContent::findOrFail($id)->update(['status' => Status::DELETED]);

        return response()->json(['success' => true, 'message' => trans('common.msg_delete_successfully')]);
    }

    /** Form fields, validated. Forms send a list of single-field objects [{ headline: … }, { landing_section: … }] */
    private function dataForm(Request $request, ?LandingContent $row = null): array
    {
        $input = $request->all();
        if (array_is_list($input)) {
            $input = array_merge(...array_filter($input, 'is_array') ?: [[]]);
        }
        $section = $input['landing_section'] ?? $row?->landing_section;
        $required = $row ? 'sometimes' : 'required';
        $fields = validator($input, [
            'landing_section' => [$required, Rule::in(LandingContent::SECTIONS)],
            'language_code' => [$required, Rule::in(Language::where('status', '!=', Status::DELETED)->pluck('code')->all())],
            'headline' => [$required, 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:2000'],
            'landing_icon' => ['nullable', Rule::in(LandingContent::ICONS)],
            // A live number needs to know which number it shows
            'landing_stat' => [$section === LandingContent::STAT && ! $row?->landing_stat ? 'required' : 'nullable', Rule::in(array_keys(LandingContent::stats()))],
            'image' => ['nullable', 'string', 'max:191'],
            'order_level' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'integer'],
        ])->validate();
        if (array_key_exists('order_level', $fields)) {
            $fields['order_level'] = (int) $fields['order_level'];
        }

        return $fields;
    }
}
