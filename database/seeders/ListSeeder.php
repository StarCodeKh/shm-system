<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Simple lists, from database/seeders/data/{table}.php (no rules beyond their key):
 *   notification_type   the types a notification is created with (Notifications form)
 *   taxable_salary      the salary tax brackets incentives are taxed with (calTaxableSalary)
 *   title               ងារ, titles of the member form: លោក, លោកស្រី, ឯកឧត្តម ...
 *   position            តំណែង, positions
 *   bank                banks of the member's bank account
 *   letter_type         letter types of incentives and requests
 * Change them later in Settings, or upload a list again from its Import screen.
 *
 * Safe to run more than once: php artisan db:seed --class=ListSeeder
 * Matched on their key (SeedData::TABLES): only missing rows are added, existing ones are never changed.
 * Runs StatusSeeder first.
 */
class ListSeeder extends Seeder
{
    private const TABLES = ['notification_type', 'taxable_salary', 'title', 'position', 'bank', 'letter_type'];

    public function run()
    {
        $this->call(StatusSeeder::class, true);
        foreach (self::TABLES as $table) {
            SeedData::seed($table, $this->command);
        }
    }
}
