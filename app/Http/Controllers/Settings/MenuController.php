<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Auth;
use CommonService;

// Administration › Menus, and the sidebar of the signed-in user (tree)
class MenuController extends Controller
{
    // Checked on writes by the module_permission middleware
    protected $PERMISSION_ACTION_CREATE = 'create_menu';
    protected $PERMISSION_ACTION_UPDATE = 'update_menu';
    protected $PERMISSION_ACTION_DELETE = 'delete_menu';

    public function __construct()
    {
        $this->middleware('auth');
    }

    // The sidebar: only what this user may open
    public function tree()
    {
        return response()->json(['data' => Menu::tree(CommonService::permission())]);
    }

    public function index(Request $request)
    {
        if (!CommonService::can('list_menu')) {
            return response()->json(['is_display' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $filter = CommonService::getFilter($request->all());
        $types = [['label' => 'ផ្នែក / Section', 'value' => 'section'], ['label' => 'ក្រុម / Group', 'value' => 'group'], ['label' => 'តំណ / Link', 'value' => 'link']];
        return response()->json([
            'data' => Menu::getData($filter),
            'total' => Menu::getTotal($filter),
            'limit' => $filter['limit'],
            'permissions' => CommonService::permission(),
            'model_name' => 'menu',
            'type' => $types,
            'parent_id' => Menu::parentOptions(),
            'permission' => Permission::where('guard_name', 'api')->orderBy('name')->pluck('name')
                ->map(fn ($name) => ['label' => $name, 'value' => $name])->values(),
            'data_fields' => Menu::dataFields(),
            'form_attributes' => Menu::formAttributes(),
            'is_display' => true,
            'message' => trans('common.msg_successfully'),
        ]);
    }

    public function show($id)
    {
        return response()->json(['success' => true, 'data' => Menu::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        $fields = $this->validated($request);
        $fields['created_by'] = Auth::id();
        $menu = Menu::create($fields);
        return response()->json(['success' => true, 'message' => trans('common.msg_save_successfully'), 'data' => $menu]);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $fields = $this->validated($request, $menu);
        $fields['modified_by'] = Auth::id();
        $menu->update($fields);
        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $menu->fresh()]);
    }

    // Items with children are kept: move or delete the children first
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        if ($menu->children()->exists()) {
            return response()->json(['success' => false, 'message' => trans('common.menu_has_children')], 422);
        }
        $menu->delete();
        return response()->json(['success' => true, 'message' => trans('common.msg_delete_successfully')]);
    }

    /** Form fields (a list of single-field objects or a plain object), checked; nesting rules from the migration */
    private function validated(Request $request, ?Menu $menu = null): array
    {
        $input = $request->all();
        $input = array_is_list($input) ? array_merge([], ...array_filter($input, 'is_array')) : $input;
        foreach (['parent_id', 'type', 'permission'] as $select) {
            if (is_array($input[$select] ?? null)) $input[$select] = $input[$select]['value'] ?? null;
        }
        $input['slug'] = ($input['slug'] ?? '') ?: ($menu->slug ?? Str::slug(($input['name'] ?? 'menu').'-'.Str::random(4)));
        $input['is_active'] = filter_var($input['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $fields = validator($input, [
            'type' => ['required', Rule::in(Menu::TYPES)],
            'parent_id' => ['nullable', 'integer', 'exists:menu,id', Rule::notIn([$menu?->id])],
            'name' => ['required', 'string', 'max:255'],
            'i18n' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'required_if:type,link', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('menu', 'slug')->ignore($menu?->id)],
            'icon' => ['nullable', 'string', 'max:64'],
            'permission' => ['nullable', 'string', 'max:255'],
            'order_level' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ])->validate();

        // Sections are top level; groups sit at the top or in a section; links anywhere but inside another link
        $parentType = isset($fields['parent_id']) ? Menu::whereKey($fields['parent_id'])->value('type') : null;
        $allowed = ['section' => [null], 'group' => [null, 'section'], 'link' => [null, 'section', 'group']][$fields['type']];
        if (!in_array($parentType, $allowed, true)) {
            abort(response()->json(['success' => false, 'message' => ['parent_id' => [trans('common.menu_bad_parent')]]], 422));
        }
        if ($fields['type'] !== 'link') $fields['url'] = null;
        $fields['order_level'] = $fields['order_level'] ?? 0;
        return $fields;
    }
}
