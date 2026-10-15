<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Chart of accounts, from database/seeders/data/account_type.php and account.php, built from the old SHM
 * system's categories (it had no account codes of its own):
 *   INC  4000 member contributions: 4100 yearly contribution, 4200 registration fee
 *   EXP  5000 benefits paid to members: 5101-5110, one per benefit type (payment_type, same order)
 * db:import-shm links the imported contributions (officer_payable / officer_payment.account_id) to 4100 / 4200
 * and the imported benefits (officer_incentive.account_id) to the account of their type.
 *
 * Safe to run more than once: php artisan db:seed --class=AccountSeeder
 * Matched on their code: only missing rows are added, existing ones are never changed. Runs StatusSeeder first.
 */
class AccountSeeder extends Seeder
{
    public function run()
    {
        $this->call(StatusSeeder::class, true);
        SeedData::seed('account_type', $this->command);
        SeedData::seed('account', $this->command);
    }
}
