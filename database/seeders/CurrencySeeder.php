<?php

namespace Database\Seeders;

use App\Models\Settings\Currency;
use App\Models\Settings\CurrencyExchange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Default currencies: KHR (id 1, the default) and USD (id 2), plus a first USD -> KHR exchange rate.
 * Officer, payment and dashboard data refer to these, so the currency dropdowns are never empty.
 *
 * Safe to run more than once: php artisan db:seed --class=CurrencySeeder
 * A currency is only added when its code (abbreviation) is missing; ones already on a server are never changed.
 * The exchange rate is only added when there is no USD / KHR rate yet; change it later in Settings.
 */
class CurrencySeeder extends Seeder
{
    private const CURRENCIES = [
        ['id' => 1, 'abbreviation' => Currency::KHR, 'name_kh' => 'រៀល',   'name_en' => 'Riel',   'symbol_left' => '៛', 'is_default' => true],
        ['id' => 2, 'abbreviation' => Currency::USD, 'name_kh' => 'ដុល្លារ', 'name_en' => 'Dollar', 'symbol_left' => '$', 'is_default' => false],
    ];

    public function run()
    {
        $now = now();
        $active = DB::table('mef_status')->where('group', 'STATUS')->where('title_en', 'Active')->value('id');
        $codes = DB::table('currency')->pluck('abbreviation')->map(fn ($code) => strtoupper(trim((string) $code)))->all();

        foreach (self::CURRENCIES as $order => $row) {
            if (in_array($row['abbreviation'], $codes, true)) continue;
            // Keep the usual id when it is free, so data moved from an older server still matches
            if (DB::table('currency')->where('id', $row['id'])->exists()) unset($row['id']);
            DB::table('currency')->insert($row + [
                'status' => $active,
                'order_level' => $order + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $khr = DB::table('currency')->where('abbreviation', Currency::KHR)->value('id');
        $usd = DB::table('currency')->where('abbreviation', Currency::USD)->value('id');
        $hasRate = DB::table('currency_exchange')
            ->where(fn ($q) => $q->where('from_currency', $usd)->where('to_currency', $khr))
            ->orWhere(fn ($q) => $q->where('from_currency', $khr)->where('to_currency', $usd))
            ->exists();
        if ($khr && $usd && !$hasRate) {
            DB::table('currency_exchange')->insert([
                'from_currency' => $usd,
                'to_currency' => $khr,
                'exchange_rate' => CurrencyExchange::DEFAULT_USD_KHR,
                'issue_date' => $now,
                'status' => $active,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
