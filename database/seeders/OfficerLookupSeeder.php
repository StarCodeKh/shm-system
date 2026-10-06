<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dropdown options for the officer form, stored in mef_status.
 * `criteria` is the value saved on the officer record, so existing data keeps working.
 *
 * Safe to run more than once: php artisan db:seed --class=OfficerLookupSeeder
 */
class OfficerLookupSeeder extends Seeder
{
    public function run()
    {
        $groups = [
            'GENDER' => [
                ['male', 'ប្រុស', 'Male'],
                ['female', 'ស្រី', 'Female'],
            ],
            'MARRIED_STATUS' => [
                ['នៅលីវ', 'នៅលីវ', 'Single'],
                ['រៀបការហើយ', 'រៀបការហើយ', 'Married'],
                ['លែងលះ', 'លែងលះ', 'Divorced'],
                ['មេម៉ាយ', 'មេម៉ាយ', 'Widowed'],
            ],
            'RELATIONSHIP' => [
                ['ឪពុក', 'ឪពុក', 'Father'],
                ['ម្តាយ', 'ម្តាយ', 'Mother'],
                ['ស្វាមី/ភរិយា', 'ស្វាមី/ភរិយា', 'Spouse'],
                ['បងប្អូន', 'បងប្អូន', 'Sibling'],
                ['មិត្តភក្តិ', 'មិត្តភក្តិ', 'Friend'],
                ['ផ្សេងៗ', 'ផ្សេងៗ', 'Other'],
            ],
        ];

        foreach ($groups as $group => $rows) {
            foreach ($rows as $i => [$value, $titleKh, $titleEn]) {
                DB::table('mef_status')->updateOrInsert(
                    ['group' => $group, 'criteria' => $value],
                    ['title_kh' => $titleKh, 'title_en' => $titleEn, 'order_level' => $i + 1, 'status' => null]
                );
            }
        }
    }
}
