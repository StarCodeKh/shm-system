<?php

namespace Database\Seeders;

use App\Models\Settings\Currency;
use App\Models\Settings\CurrencyExchange;
use App\Models\Settings\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Currencies from database/seeders/data/currency.php: KHR (id 1, the default) and USD (id 2),
 * plus a first USD -> KHR exchange rate. Officer, payment and dashboard data refer to these, so the currency
 * dropdowns are never empty.
 *
 * Safe to run more than once: php artisan db:seed --class=CurrencySeeder
 * A currency is only added when its code (abbreviation) is missing; ones already on a server are never changed.
 * Its usual id is kept when free, so data moved from an older server still matches.
 * The exchange rate is only added when there is no USD / KHR rate yet; change it later in Settings.
 * Runs StatusSeeder first.
 */
class CurrencySeeder extends Seeder
{
    public function run()
    {
        $this->call(StatusSeeder::class, true);
        $now = now();
        SeedData::seed('currency', $this->command);

        $khr = DB::table('currency')->where('abbreviation', Currency::KHR)->value('id');
        $usd = DB::table('currency')->where('abbreviation', Currency::USD)->value('id');
        $hasRate = DB::table('currency_exchange')
            ->where(fn ($q) => $q->where('from_currency', $usd)->where('to_currency', $khr))
            ->orWhere(fn ($q) => $q->where('from_currency', $khr)->where('to_currency', $usd))
            ->exists();
        if ($khr && $usd && ! $hasRate) {
            DB::table('currency_exchange')->insert([
                'from_currency' => $usd,
                'to_currency' => $khr,
                'exchange_rate' => CurrencyExchange::DEFAULT_USD_KHR,
                'issue_date' => $now,
                'status' => Status::ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
