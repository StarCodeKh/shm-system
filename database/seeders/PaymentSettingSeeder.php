<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Benefit types, contribution schedules and payment methods, from database/seeders/data/
 * payment_type.php, payment_term.php and payment_method.php. Runs StatusSeeder and CurrencySeeder first
 * (amounts are in riel).
 *
 * Safe to run more than once: php artisan db:seed --class=PaymentSettingSeeder
 * Matched on the Khmer name (payment terms: on type): only missing rows are added, existing ones are never changed.
 * A default payment term is only marked when the server has none.
 */
class PaymentSettingSeeder extends Seeder
{
    public function run()
    {
        $this->call([StatusSeeder::class, CurrencySeeder::class], true);
        SeedData::seed('payment_type', $this->command);

        $terms = SeedData::rows('payment_term');
        if (DB::table('payment_term')->where('is_default', true)->exists()) {
            $terms = array_map(fn ($term) => ['is_default' => false] + $term, $terms);
        }
        SeedData::seed('payment_term', $this->command, $terms);

        SeedData::seed('payment_method', $this->command);
    }
}
