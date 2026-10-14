<?php

namespace Database\Seeders;

use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Status rows and dropdown options (tbl_mef_status), from database/seeders/data/mef_status.php.
 * Runs first: every status column has a foreign key to this table.
 *
 *   rows with an id: statuses the code uses by id (Status::DELETED = 4, Status::ACTIVE = 13, paid 19 ...,
 *                    the FIXED_GROUPS); each is put at exactly that id
 *   rows without id: dropdown options (gender, married status, relationship, NSSF), matched on group + title_en;
 *                    gender, married status and relationship are saved on a member by their criteria value
 *
 * Safe to run more than once: php artisan db:seed --class=StatusSeeder
 * Rows already in place are never changed. When a fixed id is taken by an option row saved by value, that row is
 * moved to a free id first. A status found at another id is moved to its fixed id when nothing refers to it yet;
 * otherwise it is reported.
 */
class StatusSeeder extends Seeder
{
    // Groups whose ids the code uses (Status::ACTIVE = 13, paid 19 ...): their rows keep a fixed id
    const FIXED_GROUPS = ['GERNAL', 'CEILING', 'FLAG', 'STATUS', 'PAYMENT', 'OFFICER'];

    // Saved on records by their criteria value, so their id can change
    private const MOVABLE_GROUPS = ['GENDER', 'MARRIED_STATUS', 'RELATIONSHIP'];

    public function run()
    {
        $now = now();
        $rows = SeedData::rows('mef_status');
        $fixed = array_filter($rows, fn ($row) => isset($row['id']));
        $reserved = array_map(fn ($row) => (int) $row['id'], $fixed);

        foreach ($fixed as $row) {
            $id = (int) $row['id'];
            $current = DB::table('mef_status')->find($id);
            if ($current && $current->title_en === $row['title_en']) {
                continue;
            }
            if ($current && ! $this->moveAway($current, $reserved)) {
                $this->command?->warn("Status id {$id} should be {$row['group']} / {$row['title_en']} but is {$current->group} / {$current->title_en}: fix it by hand.");

                continue;
            }

            // Already added without its id (e.g. by an older seeder): move it to the fixed id
            $elsewhere = DB::table('mef_status')->where('group', $row['group'])->where('title_en', $row['title_en'])->value('id');
            if ($elsewhere) {
                try {
                    DB::table('mef_status')->where('id', $elsewhere)->update(['id' => $id, 'updated_at' => $now]);
                } catch (QueryException) {
                    $this->command?->warn("Status {$row['group']} / {$row['title_en']} is id {$elsewhere} and already in use, expected id {$id}: fix it by hand.");
                }

                continue;
            }

            SeedData::addMissing('mef_status', [$row], ['id'], $this->command);
        }

        SeedData::seed('mef_status', $this->command, array_filter($rows, fn ($row) => ! isset($row['id'])));
    }

    /** Move an option row off a fixed id, to the first id after all fixed and existing ones */
    private function moveAway(object $row, array $reserved): bool
    {
        if (! in_array($row->group, self::MOVABLE_GROUPS, true)) {
            return false;
        }
        $free = max((int) DB::table('mef_status')->max('id'), max($reserved)) + 1;
        try {
            DB::table('mef_status')->where('id', $row->id)->update(['id' => $free]);
        } catch (QueryException) {
            return false; // referred to by id somewhere: leave it
        }

        return true;
    }
}
