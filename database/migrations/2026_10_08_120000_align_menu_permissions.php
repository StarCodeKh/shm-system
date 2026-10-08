<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sidebar items whose permission didn't match what their page's API checks:
 * the item showed for roles that then got "no permission", or hid from roles that could use the page.
 * Only rows still holding the old value change, so permissions set in Administration › Menus are kept.
 */
return new class extends Migration
{
    private const CHANGES = [
        // url => [old permission, new permission]
        '/account/officer-payment' => ['list_officer_payment', 'list_officer_payable'],
        '/account/officer-payable-form' => ['list_officer_payable', 'create_officer_payable'],
        '/auditrail' => [null, 'permission_roles'],
    ];

    public function up(): void
    {
        foreach (self::CHANGES as $url => [$old, $new]) {
            $this->swap($url, $old, $new);
        }
    }

    public function down(): void
    {
        foreach (self::CHANGES as $url => [$old, $new]) {
            $this->swap($url, $new, $old);
        }
    }

    private function swap(string $url, ?string $from, ?string $to): void
    {
        $query = DB::table('menu')->where('url', $url);
        $from === null ? $query->whereNull('permission') : $query->where('permission', $from);
        $query->update(['permission' => $to, 'updated_at' => now()]);
    }
};
