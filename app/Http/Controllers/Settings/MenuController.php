<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Menu;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        if (! CommonService::can('list_menu')) {
            return response()->json(['is_display' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $filter = CommonService::getFilter($request->all());
        // Labels are shown in the user's language by the page (menu_type_*, permission "action · module")
        $types = array_map(fn ($type) => ['label' => $type, 'value' => $type], Menu::TYPES);

        return response()->json([
            'data' => Menu::getData($filter),
            'total' => Menu::getTotal($filter),
            'limit' => $filter['limit'],
            'permissions' => CommonService::permission(),
            'model_name' => 'menu',
            'type' => $types,
            'parent_id' => Menu::parentOptions(),
            'permission' => $this->permissionChoices(),
            'data_fields' => Menu::dataFields(),
            'form_attributes' => Menu::formAttributes(),
            'is_display' => true,
            'message' => trans('common.msg_successfully'),
        ]);
    }

    /**
     * The menu manager page: every item (hidden ones too) in sidebar order, the form choices,
     * the roles to preview the sidebar as, and what this user may change.
     */
    public function manage()
    {
        if (! CommonService::can('list_menu')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }

        return response()->json([
            'data' => Menu::orderBy('order_level')->orderBy('id')
                ->get(['id', 'parent_id', 'type', 'name', 'i18n', 'url', 'slug', 'icon', 'permission', 'order_level', 'is_active']),
            'types' => Menu::TYPES,
            // Always-on items (and the sections / groups holding them): no hide, no delete
            'locked' => Menu::lockedIds(),
            'permission' => $this->permissionChoices(),
            'roles' => Role::where('guard_name', 'api')->orderBy('id')->get(['id', 'name']),
            'can' => [
                'create' => CommonService::can('create_menu'),
                'update' => CommonService::can('update_menu'),
                'delete' => CommonService::can('delete_menu'),
            ],
        ]);
    }

    /** New order of the items under one parent: { parent_id, ids: [...] } (ids in their new order) */
    public function reorder(Request $request)
    {
        if (! CommonService::can('update_menu')) {
            return response()->json(['success' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
        ]);
        $siblings = Menu::where('parent_id', $data['parent_id'] ?? null)->pluck('id')->all();
        if (array_diff($data['ids'], $siblings)) {
            return response()->json(['success' => false, 'message' => trans('common.menu_bad_parent')], 422);
        }
        foreach (array_values($data['ids']) as $i => $id) {
            Menu::whereKey($id)->update(['order_level' => ($i + 1) * 10, 'modified_by' => Auth::id()]);
        }

        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully')]);
    }

    /** Show / hide one item: { is_active } */
    public function active(Request $request, $id)
    {
        if (! CommonService::can('update_menu')) {
            return response()->json(['success' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $menu = Menu::findOrFail($id);
        if (! $request->boolean('is_active') && in_array($menu->id, Menu::lockedIds(), true)) {
            return response()->json(['success' => false, 'message' => trans('common.menu_locked')], 422);
        }
        $menu->update(['is_active' => $request->boolean('is_active'), 'modified_by' => Auth::id()]);

        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $menu]);
    }

    /** The sidebar as one role sees it (same rules as the real sidebar): ?role_id= */
    public function preview(Request $request)
    {
        if (! CommonService::can('list_menu')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $role = Role::findOrFail((int) $request->query('role_id'));
        $permissions = CommonService::isSuperAdminRole($role)
            ? ['*' => true]
            : $role->permissions()->pluck('name')->mapWithKeys(fn ($name) => [$name => true])->all();

        return response()->json(['data' => Menu::tree($permissions)]);
    }

    /** Permission choices for the form: { label, value, module } (the page labels them "action · module") */
    private function permissionChoices()
    {
        return Permission::query()->where('permissions.guard_name', 'api')
            ->leftJoin('permission_group', 'permission_group.id', '=', 'permissions.group')
            ->orderBy('permission_group.order_level')->orderBy('permissions.id')
            ->get(['permissions.name', 'permission_group.name as module'])
            ->map(fn ($p) => ['label' => $p->name, 'value' => $p->name, 'module' => $p->module])->values();
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
        if (in_array($menu->id, Menu::lockedIds(), true)) {
            $fields['is_active'] = true;
        } // always on
        $fields['modified_by'] = Auth::id();
        $menu->update($fields);

        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $menu->fresh()]);
    }

    // Items with children are kept: move or delete the children first
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        if (in_array($menu->id, Menu::lockedIds(), true)) {
            return response()->json(['success' => false, 'message' => trans('common.menu_locked')], 422);
        }
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
            if (is_array($input[$select] ?? null)) {
                $input[$select] = $input[$select]['value'] ?? null;
            }
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
        if (! in_array($parentType, $allowed, true)) {
            abort(response()->json(['success' => false, 'message' => ['parent_id' => [trans('common.menu_bad_parent')]]], 422));
        }
        if ($fields['type'] !== 'link') {
            $fields['url'] = null;
        }
        $fields['order_level'] = $fields['order_level'] ?? 0;

        return $fields;
    }
}
