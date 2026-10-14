<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * The ministry's general departments (tbl_entity) and their sections (tbl_department),
 * from database/seeders/data/entity.php and department.php (the old SHM system's business / operating units).
 * Members are linked to both: officer.entity_id and officer.department_id.
 *
 * Safe to run more than once: php artisan db:seed --class=OrganizationSeeder
 * Matched on code: only missing codes are added, names already on a server are never changed.
 * Runs StatusSeeder first.
 */
class OrganizationSeeder extends Seeder
{
    public function run()
    {
        $this->call(StatusSeeder::class, true);
        SeedData::seed('entity', $this->command);
        SeedData::seed('department', $this->command);
    }
}
