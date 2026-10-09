<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The sidebar menu (tbl_menu), from database/seeders/data/menu.php.
 *
 * Safe to run more than once: rows are matched by slug and only missing ones are added,
 * so items edited in Administration › Menus keep their changes.
 */
class MenuSeeder extends Seeder
{
    public function run()
    {
        $this->insert(require __DIR__.'/data/menu.php', null);
    }

    private function insert(array $rows, ?int $parentId): void
    {
        foreach ($rows as $order => $row) {
            [$type, $slug, $name, $i18n, $icon, $url, $permission] = $row;
            $id = DB::table('menu')->where('slug', $slug)->value('id') ?? DB::table('menu')->insertGetId([
                'parent_id' => $parentId,
                'type' => $type,
                'name' => $name,
                'i18n' => $i18n,
                'icon' => $icon,
                'url' => $url,
                'slug' => $slug,
                'permission' => $permission,
                'order_level' => ($order + 1) * 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (! empty($row[7])) {
                $this->insert($row[7], $id);
            }
        }
    }
}
