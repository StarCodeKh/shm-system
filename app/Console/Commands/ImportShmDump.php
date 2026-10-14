<?php

namespace App\Console\Commands;

use App\Models\Settings\Currency;
use App\Models\Settings\Status;
use App\Services\ShmLegacy;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\PaymentSettingSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Imports the members, contributions and benefits of the old SHM system (Django, PostgreSQL) from its pg_dump file,
 * straight into the database. The dump is read directly: no PostgreSQL server is needed.
 * (db:export-shm gives the same data as Excel files for the list imports instead.)
 *
 *   php artisan db:import-shm /path/to/shm-mef.sql --dry-run    (check and count, save nothing)
 *   php artisan db:import-shm /path/to/shm-mef.sql
 *
 * What maps to what: App\Services\ShmLegacy. Each member also gets a yearly officer_payment_recurring, like one
 * added in the app. Business / operating units, benefit types and statuses come from the seeders run first.
 * Old logins, permissions, sessions, logs and the *_bk / tmp_rec copies are not imported.
 *
 * Safe to run more than once: members are matched on their member number (cus_code -> identity_member_no) and
 * existing ones are left as they are. Receipts and benefits are imported once (marked with code OLD);
 * --fresh deletes the imported ones and imports them again. Payables and payments made in the app are never touched.
 * Staff are linked as created_by when a user with the same username exists in the app.
 * Photos and scanned files are not in the dump: copy the old media folder (img/, doc/) to public{--media-url}.
 */
class ImportShmDump extends Command
{
    protected $signature = 'db:import-shm
        {file : The old system\'s pg_dump file (plain .sql)}
        {--media-url=/images/uploads/legacy : URL path where the old img/ and doc/ folders are copied}
        {--fresh : Delete the receipts and benefits imported before, then import them again}
        {--dry-run : Check and count everything, then roll back}';

    protected $description = 'Import members, contributions and benefits from the old SHM system\'s PostgreSQL dump';

    // Marks the payables and benefits made by this import (officer_payable.code is 5 characters)
    private const CODE = 'OLD';

    private const CHUNK = 1000;

    private ShmLegacy $dump;

    private array $users;

    private array $warnings = [];

    public function handle()
    {
        if (! ShmLegacy::isDump($this->argument('file'))) {
            $this->error("{$this->argument('file')} is not a readable PostgreSQL dump.");

            return 1;
        }
        $this->dump = new ShmLegacy($this->argument('file'), $this->option('media-url'));

        DB::beginTransaction();
        try {
            foreach ([StatusSeeder::class, CurrencySeeder::class, PaymentSettingSeeder::class, OrganizationSeeder::class] as $seeder) {
                $this->callSilent('db:seed', ['--class' => $seeder, '--force' => true]);
            }
            $this->users = DB::table('users')->pluck('id', 'username')->all();

            $members = $this->importMembers();
            $this->importReceipts($members);
            $this->importBenefits($members);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Nothing was saved: '.$e->getMessage());

            return 1;
        }

        foreach (array_unique(array_merge($this->warnings, $this->dump->warnings)) as $warning) {
            $this->warn("  {$warning}");
        }
        if ($this->option('dry-run')) {
            DB::rollBack();
            $this->info('Dry run: everything was checked, nothing was saved.');
        } else {
            DB::commit();
            $this->info('Import finished.');
        }

        return 0;
    }

    /** @return array old customer id => officer id */
    private function importMembers(): array
    {
        $entities = DB::table('entity')->whereNotNull('code')->pluck('id', 'code')->all();
        $departments = DB::table('department')->whereNotNull('code')->pluck('id', 'code')->all();
        $existing = DB::table('officer')->pluck('id', 'identity_member_no')->all();

        $new = [];
        $files = [];
        $codes = [];
        foreach ($this->dump->rows('customer_customer') as $row) {
            $code = (string) $row['cus_code'];
            $codes[$row['id']] = $code;
            if (isset($existing[$code])) {
                continue;
            }
            $new[] = $this->dump->officer($row, $entities[$row['business_unit']] ?? null, ShmLegacy::departmentId($departments, $row['operating_unit']));
            if ($file = $this->dump->document($row)) {
                $files[$code] = $file;
            }
        }
        foreach (array_chunk($new, self::CHUNK) as $chunk) {
            DB::table('officer')->insert($chunk);
        }

        $ids = DB::table('officer')->whereIn('identity_member_no', array_values($codes))->pluck('id', 'identity_member_no')->all();
        $members = array_map(fn ($code) => $ids[$code], $codes);

        // Scanned member file
        $documents = [];
        foreach ($files as $code => $path) {
            $documents[] = ['officer_id' => $ids[$code], 'document_name' => basename($path), 'attach' => $path, 'status' => Status::ACTIVE, 'created_at' => now(), 'updated_at' => now()];
        }
        foreach (array_chunk($documents, self::CHUNK) as $chunk) {
            DB::table('officer_document')->insert($chunk);
        }

        // Every member has a yearly contribution schedule from the register date, like a member added in the app
        $term = DB::table('payment_term')->where('is_default', true)->first();
        $scheduled = DB::table('officer_payment_recurring')->pluck('officer_id')->flip()->all();
        $officers = DB::table('officer')->whereIn('id', $ids)->get(['id', 'status', 'register_date'])->keyBy('id');
        $khr = Currency::idOf(Currency::KHR);
        $schedules = [];
        foreach ($officers as $id => $officer) {
            if (isset($scheduled[$id])) {
                continue;
            }
            $schedules[] = [
                'officer_id' => $id,
                'payment_term' => $term->type ?? 'Yearly',
                'recurring_amount' => $term->recurring_amount ?? 60000,
                'currency_id' => $khr,
                'executed_num' => 1,
                'schedule_start_date' => $officer->register_date ?? now()->toDateString(),
                'schedule_end_date' => '2100-01-01',
                'remark' => '',
                'status' => $officer->status == Status::ACTIVE ? Status::ACTIVE : Status::INACTIVE,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach (array_chunk($schedules, self::CHUNK) as $chunk) {
            DB::table('officer_payment_recurring')->insert($chunk);
        }

        $this->line(sprintf('  <info>ok</info>    members: %d in the dump, %d added, %d already here; %d files, %d payment schedules',
            count($codes), count($new), count($codes) - count($new), count($documents), count($schedules)));

        return $members;
    }

    private function importReceipts(array $members): void
    {
        $imported = DB::table('officer_payable')->where('code', self::CODE);
        if ($imported->exists()) {
            if (! $this->option('fresh')) {
                $this->line('  <comment>skip</comment>  receipts (imported before, use --fresh to import them again)');

                return;
            }
            DB::table('officer_payment')->whereIn('ref_payable_id', (clone $imported)->select('id'))->delete();
            $imported->delete();
        }

        // Ids are given here, so each payment can point to its payable without reading them back
        $nextId = (int) DB::table('officer_payable')->max('id') + 1;
        $payables = [];
        $payments = [];
        $count = ['paid' => 0, 'void' => 0];
        $flush = function () use (&$payables, &$payments) {
            DB::table('officer_payable')->insert($payables);
            if ($payments) {
                DB::table('officer_payment')->insert($payments);
            }
            $payables = $payments = [];
        };

        foreach ($this->dump->rows('customer_receive') as $row) {
            $officer = $members[$row['customer_id']] ?? null;
            if (! $officer) {
                $this->warnings[] = "Receipt {$row['receipt_id']}: its member is not in the dump, skipped.";

                continue;
            }
            $by = ['created_by' => $this->users[$row['create_by']] ?? null];
            $id = $nextId++;
            $payables[] = ['id' => $id, 'code' => self::CODE] + $this->dump->payable($row, $officer) + $by;
            $payment = $this->dump->payment($row, $officer, $id);
            if ($payment) {
                $payments[] = $payment + $by;
            }
            $count[$payment ? 'paid' : 'void']++;
            if (count($payables) >= self::CHUNK) {
                $flush();
            }
        }
        if ($payables) {
            $flush();
        }

        $this->line("  <info>ok</info>    receipts: {$count['paid']} paid (payable + payment), {$count['void']} cancelled (Void payable)");
    }

    private function importBenefits(array $members): void
    {
        $imported = DB::table('officer_incentive')->where('code', 'like', self::CODE.'-%');
        if ($imported->exists()) {
            if (! $this->option('fresh')) {
                $this->line('  <comment>skip</comment>  benefits (imported before, use --fresh to import them again)');

                return;
            }
            $imported->delete();
        }

        $rows = [];
        foreach ($this->dump->rows('customer_payment') as $row) {
            $officer = $members[$row['customer_id']] ?? null;
            if (! $officer) {
                $this->warnings[] = "Benefit {$row['payment_id']}: its member is not in the dump, skipped.";

                continue;
            }
            $rows[] = ['code' => self::CODE.'-'.$row['payment_id']] + $this->dump->incentive($row, $officer)
                + ['created_by' => $this->users[$row['create_by']] ?? null];
        }
        foreach (array_chunk($rows, self::CHUNK) as $chunk) {
            DB::table('officer_incentive')->insert($chunk);
        }

        $this->line('  <info>ok</info>    benefits: '.count($rows).' paid to members');
    }
}
