<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

// One sidebar entry: a section heading, a collapsible group or a link (see the create_menu_table migration)
class Menu extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public const TYPES = ['section', 'group', 'link'];

    // Items that are always on: they can't be hidden or deleted, so nobody can lock themselves out of
    // managing the menu. Their sections / groups can't be hidden either (that would hide them too).
    public const LOCKED_URLS = ['/menu'];

    /** Ids of the locked items and of every section / group above them */
    public static function lockedIds(): array
    {
        $rows = self::get(['id', 'parent_id', 'url'])->keyBy('id');
        $ids = [];
        foreach ($rows as $row) {
            if (!in_array($row->url, self::LOCKED_URLS, true)) continue;
            for ($node = $row; $node; $node = $rows->get($node->parent_id)) {
                $ids[$node->id] = true;
            }
        }
        return array_keys($ids);
    }

    protected $table = 'menu';
    protected $fillable = ['parent_id', 'type', 'name', 'i18n', 'url', 'slug', 'icon', 'permission', 'order_level', 'is_active', 'created_by', 'modified_by'];
    protected $casts = ['is_active' => 'boolean', 'order_level' => 'integer', 'parent_id' => 'integer'];
    protected $appends = ['parent_name'];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent_id ? $this->parent?->name : null;
    }

    // Columns of the list in Administration › Menus
    public static function dataFields()
    {
        return ['name', 'type', 'parent_name', 'url', 'icon', 'permission', 'order_level', 'is_active'];
    }

    // Add / edit form. Choices for the selects come with the list response (parent_id, type, permission).
    public static function formAttributes()
    {
        return [
            ['name' => 'type', 'type' => 'select', 'required' => true],
            ['name' => 'parent_id', 'type' => 'select', 'required' => false],
            ['name' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'i18n', 'type' => 'text', 'required' => false],
            ['name' => 'url', 'type' => 'text', 'required' => false],
            ['name' => 'icon', 'type' => 'text', 'required' => false],
            ['name' => 'permission', 'type' => 'select', 'required' => false],
            ['name' => 'order_level', 'type' => 'number', 'required' => false],
            ['name' => 'is_active', 'type' => 'checkbox', 'required' => false, 'default' => true],
        ];
    }

    public static function getData($filter)
    {
        return self::search($filter)->with('parent')
            ->offset(($filter['page_number'] - 1) * $filter['limit'])->limit($filter['limit'])->get();
    }

    public static function getTotal($filter)
    {
        return self::search($filter)->count();
    }

    // In menu order (parent, then order_level) unless the list asks for another sort; optional name / url search
    protected static function search($filter)
    {
        $query = self::query();
        foreach ((array) ($filter['search_field'] ?: []) as $field) {
            foreach ($field as $name => $value) {
                $value = is_array($value) ? ($value['value'] ?? null) : $value;
                if (in_array($name, ['name', 'url', 'slug', 'permission', 'type'], true) && $value !== null && $value !== '') {
                    $query->where($name, 'like', '%'.$value.'%');
                }
            }
        }
        return ($filter['sort'] ?? 'id') === 'id'
            ? $query->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, order_level, id')
            : $query->orderByFilter($filter);
    }

    // Choices for the "parent" select: sections and groups, as "Section › Group"
    public static function parentOptions()
    {
        return self::whereIn('type', ['section', 'group'])->with('parent')->orderBy('order_level')->get()
            ->map(fn ($m) => ['label' => trim(($m->parent ? $m->parent->name.' › ' : '').$m->name), 'value' => $m->id])
            ->sortBy('label')->values()->all();
    }

    /**
     * The sidebar for a user with these permissions ([name => true], '*' = everything):
     * active rows only, in order, in the shape the sidebar components read:
     *   section: { header, i18n, slug, items: [...] }   group: { name, i18n, icon, slug, submenu: [...] }   link: { name, i18n, icon, slug, url }
     * Rows the user may not open are left out, and so are sections and groups that end up empty.
     */
    public static function tree($permissions): array
    {
        $rows = self::where('is_active', true)->orderBy('order_level')->orderBy('id')->get()->groupBy('parent_id');
        $allowed = fn ($m) => !$m->permission || !empty($permissions['*']) || !empty($permissions[$m->permission]);

        $build = function ($parentId) use (&$build, $rows, $allowed) {
            $list = [];
            foreach ($rows->get($parentId ?? '', collect()) as $m) {
                if (!$allowed($m)) continue;
                $base = ['name' => $m->name, 'i18n' => $m->i18n, 'slug' => $m->slug, 'icon' => $m->icon];
                if ($m->type === 'link') {
                    if ($m->url) $list[] = $base + ['url' => $m->url];
                    continue;
                }
                $children = $build($m->id);
                if (!$children) continue;
                $list[] = $m->type === 'section'
                    ? ['header' => $m->name, 'i18n' => $m->i18n, 'slug' => $m->slug, 'items' => $children]
                    : $base + ['submenu' => $children];
            }
            return $list;
        };
        return $build(null);
    }
}
