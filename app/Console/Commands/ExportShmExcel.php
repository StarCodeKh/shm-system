<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImportController;
use App\Models\Settings\Currency;
use App\Models\Settings\Status;
use App\Services\ShmLegacy;
use App\Services\TemplateWorkbook;
use Database\Seeders\SeedData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Writes the old SHM system's data (its pg_dump file) as Excel files for the list imports, one table at a time:
 * each file has the headings of that list's "download template" (GET /api/v1/import/template), with this
 * system's ids and values. Import them in their number order (a file links to the ones before it):
 *
 *   php artisan db:seed                                    (statuses, currency: the files use their ids)
 *   php artisan db:export-shm /path/to/shm-mef.sql --out=/path/to/folder
 *
 *   00_old_system.xlsx         every old table and column: where it went, or why it is not needed
 *   00_mapping.xlsx            every new column: where it comes from (old table.column) and how it is converted
 *   01_department.xlsx         Settings > Department          (old operating units)
 *   02_entity.xlsx             Settings > Entity              (old business units)
 *   03_payment_type.xlsx       Settings > Payment Type        (old benefit types)
 *   04_officer.xlsx            Member                         (old customers)
 *   05_officer_payable_NN.xlsx Member > Payable               (old receipt lines; cancelled ones are Void)
 *   06_officer_payment_NN.xlsx Member > Payment               (money received for the paid payables)
 *   07_officer_incentive.xlsx  Member > Incentive             (old benefits paid)
 * Big tables are split in files of --rows rows (the import preview checks up to 10,000 rows per file).
 *
 * Ids are taken from the database the files will be imported into: units, benefit types and members already
 * there keep their id (matched on code, Khmer name, member number), so importing them again only updates them.
 * New rows get the next free ids, so export right before importing, into that same database.
 * Mapping: App\Services\ShmLegacy (the same one db:import-shm uses).
 */
class ExportShmExcel extends Command
{
    protected $signature = 'db:export-shm
        {file : The old system\'s pg_dump file (plain .sql)}
        {--out= : Folder for the Excel files (default: storage/app/shm-excel)}
        {--rows=10000 : Rows per file for the big tables}
        {--media-url=/images/uploads/legacy : URL path where the old img/ and doc/ folders are copied}';

    protected $description = 'Write the old SHM system\'s data as Excel files in the import templates\' format';

    // Statuses the files use: they must be these ids in the database (StatusSeeder)
    private const STATUSES = [
        Status::ACTIVE => 'Active', Status::INACTIVE => 'Inactive', Status::DEAD => 'Dead', Status::RETIRED => 'Retired',
        Status::PAID => 'Paid', Status::VOID => 'Void', Status::COMPLETED => 'Completed', Status::DELETED => 'Deleted',
    ];

    // file => [column => [old source, how]]; columns not listed are left empty
    private const MAPPING = [
        'department' => [
            'id' => ['', 'id of the department with this code, else a new id'],
            'code' => ['master_data_operatingunit.operating_unit', 'as is (first 2 digits = its entity\'s code)'],
            'name_en' => ['master_data_operatingunit.description', 'the Khmer name (no English in the old system)'],
            'name_kh' => ['master_data_operatingunit.description', 'as is'],
            'order_level' => ['', 'order of the codes'],
            'status' => ['master_data_operatingunit.eff_status', 'A -> 13 Active'],
        ],
        'entity' => [
            'id' => ['', 'id of the entity with this code, else a new id'],
            'code' => ['master_data_businessunit.business_unit', 'as is'],
            'parent_id' => ['', '0 (a top unit)'],
            'name_en' => ['master_data_businessunit.description', 'the Khmer name (no English in the old system)'],
            'name_kh' => ['master_data_businessunit.description', 'as is'],
            'order_level' => ['', 'order of the codes'],
            'status' => ['master_data_businessunit.eff_status', 'A -> 13 Active'],
            'is_active' => ['', '1'],
        ],
        'payment_type' => [
            'id' => ['', 'id of the payment type with this Khmer name, else a new id'],
            'name_kh' => ['customer_paymenttype.description', 'as is'],
            'name_en' => ['customer_paymenttype.description', 'English name from database/seeders/data/payment_type.php'],
            'amount' => ['customer_paymenttype.amount', 'riel'],
            'currency_id' => ['', 'KHR'],
            'order_level' => ['', 'order in the old system'],
            'status' => ['customer_paymenttype.is_active', 't -> 13 Active, f -> 14 Inactive'],
        ],
        'officer' => [
            'id' => ['', 'id of the member with this member number, else a new id'],
            'first_name' => ['customer_customer.first_name', 'trimmed, invisible zero-width characters removed'],
            'last_name' => ['customer_customer.last_name', 'trimmed, invisible zero-width characters removed'],
            'identity_member_no' => ['customer_customer.cus_code', 'as is (the member number)'],
            'photo' => ['customer_customer.img', '--media-url + path (copy the old img/ folder there)'],
            'gender' => ['customer_customer.gender', 'ប្រុស -> male, ស្រី -> female'],
            'dob' => ['customer_customer.birth_date', 'as is; impossible years (0004, 0199 ...) left empty'],
            'register_date' => ['customer_customer.register_date', 'as is'],
            'entity_id' => ['customer_customer.business_unit', 'id of the entity with that code (empty when unknown)'],
            'department_id' => ['customer_customer.operating_unit', 'id of the department with that code (empty when unknown)'],
            'telephone_1' => ['customer_customer.phone', 'trimmed'],
            'status' => ['customer_customer.is_dead / is_retire / is_active', 'dead -> 26 Dead, retired -> 27 Retired, not active -> 14 Inactive, else 13 Active'],
            'remark' => ['customer_customer.username', 'the old full name, only when typed differently from first + last name'],
        ],
        'officer_payable' => [
            'id' => ['', 'new id (the payment file links to it)'],
            'officer_id' => ['customer_receive.customer_id', 'id of that member'],
            'amount' => ['customer_receive.price', 'riel'],
            'currency_id' => ['', 'KHR'],
            'payment_term' => ['customer_receive.is_register', 't -> Register (registration fee), f -> Yearly'],
            'payment_date' => ['customer_receive.receive_date', 'date, local time; a wrong year (0204) takes the year of entry_date'],
            'due_date' => ['customer_receive.year', '1 January of that year'],
            'status' => ['customer_receive.is_cancel', 't -> 16 Void, f -> 19 Paid'],
            'remark' => ['customer_receive.year / month_remark / month / bu / ou', '"វិភាគទានឆ្នាំ{year} ខែ{months}" (+ "(6 ខែ)" when not 12) or the registration fee; + " · នៅ {unit}" when paid in another unit'],
        ],
        'officer_payment' => [
            'id' => ['', 'new id'],
            'officer_id' => ['customer_receive.customer_id', 'id of that member'],
            'ref_payable_id' => ['', 'id of its row in the payable file'],
            'receipt_no' => ['customer_receive.receipt_id', 'as is (one receipt can pay several years)'],
            'amount' => ['customer_receive.price', 'riel'],
            'payment_date' => ['customer_receive.receive_date', 'local time; a wrong year takes the year of entry_date'],
            'issue_date' => ['customer_receive.receive_date', 'local time'],
            'currency_id' => ['', 'KHR'],
            'note' => ['customer_receive.receive_by', '"ទទួលដោយ {name}"'],
            'status' => ['', '19 Paid (cancelled receipts have no payment)'],
        ],
        'officer_incentive' => [
            'id' => ['', 'new id'],
            'officer_id' => ['customer_payment.customer_id', 'id of that member'],
            'request_title' => ['customer_payment.payment_type', 'the benefit type\'s Khmer name'],
            'amount' => ['customer_payment.amount', 'riel'],
            'currency_id' => ['', 'KHR'],
            'request_date' => ['customer_payment.payment_date', 'local time; a wrong year (0023) takes the year of entry_date'],
            'issue_date' => ['customer_payment.payment_date', 'local time'],
            'reference' => ['customer_payment.payment_id', 'as is'],
            'tax' => ['', '0'],
            'amount_after_tax' => ['customer_payment.amount', 'no tax'],
            'is_notify' => ['', '0'],
            'status' => ['customer_payment.is_active', 't -> 8 Completed, f -> 4 Deleted'],
            'remark' => ['customer_payment.description / record_by / bu / ou', 'description + "ឧបត្ថម្ភដោយ {name}" (+ " · នៅ {unit}" when given in another unit)'],
        ],
    ];

    // Every table of the old system: where each column went, or why it was not needed.
    // table => [new table, [column => [new column, how]]], or table => [null, why the whole table is not moved]
    private const OLD_SYSTEM = [
        'customer_customer' => ['officer', [
            'id' => ['', 'links receipts and benefits to the member (not stored)'],
            'first_name' => ['officer.first_name', 'invisible zero-width characters removed'],
            'last_name' => ['officer.last_name', 'invisible zero-width characters removed'],
            'username' => ['officer.remark', 'kept only when typed differently from first + last name'],
            'gender' => ['officer.gender', 'ប្រុស -> male, ស្រី -> female'],
            'cus_code' => ['officer.identity_member_no', 'as is'],
            'birth_date' => ['officer.dob', 'impossible years (0004, 0199 ...) left empty'],
            'operating_unit' => ['officer.department_id', 'id of the department with that code'],
            'register_date' => ['officer.register_date', 'as is'],
            'phone' => ['officer.telephone_1', 'trimmed'],
            'document' => ['officer_document.attach', 'db:import-shm only (scanned files have no Excel import)'],
            'business_unit' => ['officer.entity_id', 'id of the entity with that code'],
            'img' => ['officer.photo', '--media-url + path'],
            'is_active' => ['officer.status', 'not active -> 14 Inactive'],
            'is_dead' => ['officer.status', '26 Dead'],
            'is_retire' => ['officer.status', '27 Retired'],
            'is_unpaid' => ['', 'not used: false for every member'],
            'created_date' => ['officer.created_at', 'db:import-shm (an Excel import sets it itself)'],
            'modified_on' => ['officer.updated_at', 'db:import-shm (an Excel import sets it itself)'],
        ]],
        'customer_receive' => ['officer_payable + officer_payment', [
            'id' => ['', 'links the payment to its payable (not stored)'],
            'receipt_no' => ['', 'the number part of receipt_id'],
            'receipt_id' => ['officer_payment.receipt_no', 'as is'],
            'customer_id' => ['officer_id', 'id of that member'],
            'price' => ['amount', 'riel'],
            'year' => ['officer_payable.due_date', '1 January of that year'],
            'create_by' => ['created_by', 'db:import-shm, when a user with that username exists'],
            'receive_by' => ['officer_payment.note', '"ទទួលដោយ {name}"'],
            'receive_date' => ['payment_date, issue_date', 'local time; a wrong year (0023, 0204) takes the year of entry_date'],
            'is_cancel' => ['status', 't -> payable 16 Void and no payment, f -> 19 Paid'],
            'is_register' => ['officer_payable.payment_term', 't -> Register (registration fee), f -> Yearly'],
            'bu' => ['officer_payable.remark', 'unit at the time of payment, when not the member\'s unit now'],
            'ou' => ['officer_payable.remark', 'unit at the time of payment, when not the member\'s unit now'],
            'month' => ['officer_payable.remark', 'number of months paid, when not 12'],
            'receipt_order' => ['', 'sort number (year + receipt number), not needed'],
            'entry_date' => ['created_at', 'db:import-shm; also the year for a wrong receive_date'],
            'month_remark' => ['officer_payable.remark', 'months paid ("1-12")'],
        ]],
        'customer_payment' => ['officer_incentive', [
            'id' => ['', 'not needed'],
            'payment_no' => ['', 'the number part of payment_id'],
            'payment_id' => ['officer_incentive.reference', 'as is'],
            'customer_id' => ['officer_incentive.officer_id', 'id of that member'],
            'document' => ['', 'empty for every benefit'],
            'create_by' => ['officer_incentive.created_by', 'db:import-shm, when a user with that username exists'],
            'record_by' => ['officer_incentive.remark', '"ឧបត្ថម្ភដោយ {name}"'],
            'payment_date' => ['request_date, issue_date', 'local time; a wrong year (0023) takes the year of entry_date'],
            'payment_type' => ['officer_incentive.request_title', 'the benefit type\'s Khmer name'],
            'description' => ['officer_incentive.remark', 'trimmed'],
            'amount' => ['amount, amount_after_tax', 'riel, no tax'],
            'entry_date' => ['officer_incentive.created_at', 'db:import-shm'],
            'is_active' => ['officer_incentive.status', 't -> 8 Completed, f -> 4 Deleted'],
            'bu' => ['officer_incentive.remark', 'unit at the time, when not the member\'s unit now'],
            'ou' => ['officer_incentive.remark', 'unit at the time, when not the member\'s unit now'],
        ]],
        'customer_paymenttype' => ['payment_type', [
            'id' => ['', 'links benefits to their type (not stored)'],
            'description' => ['payment_type.name_kh', 'as is (name_en: database/seeders/data/payment_type.php)'],
            'amount' => ['payment_type.amount', 'riel'],
            'is_active' => ['payment_type.status', '13 Active / 14 Inactive'],
        ]],
        'master_data_businessunit' => ['entity', [
            'id' => ['', 'not needed: the code is the key'],
            'business_unit' => ['entity.code', 'as is'],
            'description' => ['entity.name_kh, name_en', 'as is'],
            'eff_date' => ['', 'no such column in the new design'],
            'eff_status' => ['entity.status', 'A -> 13 Active'],
        ]],
        'master_data_operatingunit' => ['department', [
            'id' => ['', 'not needed: the code is the key'],
            'operating_unit' => ['department.code', 'as is'],
            'description' => ['department.name_kh, name_en', 'as is'],
            'eff_date' => ['', 'no such column in the new design'],
            'eff_status' => ['department.status', 'A -> 13 Active'],
        ]],
        'master_data_setprice' => ['payment_term', [
            'id' => ['', 'not needed'],
            'year' => ['payment_term', 'the current year\'s fee is the Yearly payment term (PaymentSettingSeeder)'],
            'price' => ['officer_payable.amount', 'each receipt keeps the amount actually paid'],
            'is_active' => ['', 'true for every year'],
        ]],
        'customer_receive_bk' => [null, 'backup copy: every row is also in customer_receive'],
        'customer_receive_bk1' => [null, 'backup copy: every row is also in customer_receive'],
        'tmp_rec' => [null, 'temporary copy: every row is also in customer_receive'],
        'master_data_systemlabel' => [null, 'titles of the old printouts; the new reports have their own (resources/lang/*/report.php)'],
        'home_notification' => [null, 'old release notes for staff ("Fixed customer can\'t update data")'],
        'auth_user' => [null, 'old staff logins: not moved (Django passwords cannot be used); create users in the app'],
        'auth_group' => [null, 'old roles: the new roles come from RolePermissionSeeder'],
        'auth_user_groups' => [null, 'old roles: the new roles come from RolePermissionSeeder'],
        'auth_permission' => [null, 'old permissions: the new ones come from RolePermissionSeeder'],
        'auth_group_permissions' => [null, 'old permissions: the new ones come from RolePermissionSeeder'],
        'auth_user_user_permissions' => [null, 'old permissions: the new ones come from RolePermissionSeeder'],
        'home_profile' => [null, 'staff Khmer names and photos: part of the old logins'],
        'home_permission' => [null, 'old menu permissions: the new ones come from RolePermissionSeeder'],
        'home_sidebar' => [null, 'old menu: the new one comes from MenuSeeder'],
        'otp_totp_totpdevice' => [null, 'old two-factor devices of the old logins'],
        'axes_accessattempt' => [null, 'old login attempt log'],
        'axes_accessfailurelog' => [null, 'old login attempt log'],
        'axes_accesslog' => [null, 'old login log'],
        'background_task' => [null, 'old job queue'],
        'background_task_completedtask' => [null, 'old job queue'],
        'django_admin_log' => [null, 'framework table (admin change log)'],
        'django_content_type' => [null, 'framework table'],
        'django_migrations' => [null, 'framework table'],
        'django_session' => [null, 'framework table (sessions)'],
    ];

    private ShmLegacy $dump;

    private string $out;

    private ?int $khr;

    public function handle()
    {
        if (! ShmLegacy::isDump($this->argument('file'))) {
            $this->error("{$this->argument('file')} is not a readable PostgreSQL dump.");

            return 1;
        }
        if ($missing = $this->missingSetup()) {
            $this->error("{$missing} Run php artisan db:seed on this database first, then export again.");

            return 1;
        }
        ini_set('memory_limit', '-1');
        $this->dump = new ShmLegacy($this->argument('file'), $this->option('media-url'));
        $this->khr = Currency::idOf(Currency::KHR);
        $this->out = rtrim($this->option('out') ?: storage_path('app/shm-excel'), '/');
        if (! is_dir($this->out) && ! mkdir($this->out, 0755, true)) {
            $this->error("Cannot create {$this->out}");

            return 1;
        }

        $this->oldSystem();
        $this->mapping();
        $departments = $this->units('department', 'master_data_operatingunit', 'operating_unit', '01_department');
        $entities = $this->units('entity', 'master_data_businessunit', 'business_unit', '02_entity', ['parent_id' => 0, 'is_active' => 1]);
        $this->paymentTypes();
        $members = $this->officers($entities, $departments);
        $this->receipts($members);
        $this->incentives($members);

        foreach (array_unique($this->dump->warnings) as $warning) {
            $this->warn("  {$warning}");
        }
        $this->info("Files written to {$this->out}. Import them in their number order.");

        return 0;
    }

    /** What the files rely on that this database doesn't have yet, or null */
    private function missingSetup(): ?string
    {
        $statuses = DB::table('mef_status')->whereIn('id', array_keys(self::STATUSES))->pluck('title_en', 'id');
        foreach (self::STATUSES as $id => $title) {
            if (($statuses[$id] ?? null) !== $title) {
                return "Status id {$id} is not \"{$title}\".";
            }
        }

        return Currency::idOf(Currency::KHR) ? null : 'There is no KHR currency.';
    }

    /** 00_old_system.xlsx: every table and column of the dump, where it went or why not; unknown ones are flagged */
    private function oldSystem(): void
    {
        $rows = [];
        $unknown = 0;
        foreach ($this->dump->tables() as $table => $columns) {
            [$to, $map] = self::OLD_SYSTEM[$table] ?? [null, null];
            foreach ($columns as $column) {
                [$into, $how] = is_array($map) ? ($map[$column] ?? ['NOT MAPPED', 'check this column']) : ['', $map ?? 'NOT MAPPED: check this table'];
                $unknown += str_starts_with($into.$how, 'NOT MAPPED') ? 1 : 0;
                $rows[] = ['old_table' => $table, 'old_column' => $column, 'new_table' => $to ?? 'not moved', 'new_column' => $into, 'how' => $how];
            }
        }
        $this->write('00_old_system', 'old_system', self::textColumns('old_table', 'old_column', 'new_table', 'new_column', 'how'), $rows);
        if ($unknown) {
            $this->warn("  {$unknown} old column(s) are not in the mapping: see NOT MAPPED in 00_old_system.xlsx.");
        }
    }

    private function mapping(): void
    {
        $rows = [];
        $n = 0;
        foreach (self::MAPPING as $table => $columns) {
            $n++;
            foreach (array_keys(ImportController::templateColumns($table)) as $column) {
                [$from, $how] = self::MAPPING[$table][$column] ?? ['', 'empty'];
                $rows[] = ['file' => sprintf('%02d_%s', $n, $table), 'column' => $column, 'from' => $from, 'how' => $how];
            }
        }
        $this->write('00_mapping', 'mapping', self::textColumns('file', 'column', 'from', 'how'), $rows);
    }

    /** Departments / entities from the old units. @return array old code => id */
    private function units(string $table, string $source, string $codeColumn, string $file, array $extra = []): array
    {
        $ids = DB::table($table)->whereNotNull('code')->pluck('id', 'code')->all();
        $next = self::nextId($table);
        $rows = [];
        $old = collect($this->dump->rows($source))->sortBy($codeColumn)->values();
        foreach ($old as $i => $unit) {
            $code = $unit[$codeColumn];
            $ids[$code] ??= $next++;
            $rows[] = $extra + [
                'id' => $ids[$code],
                'code' => $code,
                'name_en' => trim($unit['description']),
                'name_kh' => trim($unit['description']),
                'order_level' => $i + 1,
                'status' => $unit['eff_status'] === 'A' ? Status::ACTIVE : Status::INACTIVE,
            ];
        }
        $this->write($file, $table, ImportController::templateColumns($table), $rows);

        return $ids;
    }

    private function paymentTypes(): void
    {
        $english = collect(SeedData::rows('payment_type'))->pluck('name_en', 'name_kh');
        $ids = DB::table('payment_type')->pluck('id', 'name_kh')->all();
        $next = self::nextId('payment_type');
        $rows = [];
        foreach ($this->dump->rows('customer_paymenttype') as $i => $type) {
            $name = trim($type['description']);
            $rows[] = [
                'id' => $ids[$name] ??= $next++,
                'name_kh' => $name,
                'name_en' => $english[$name] ?? $name,
                'amount' => $type['amount'],
                'currency_id' => $this->khr,
                'order_level' => $i + 1,
                'status' => $type['is_active'] === 't' ? Status::ACTIVE : Status::INACTIVE,
            ];
        }
        $this->write('03_payment_type', 'payment_type', ImportController::templateColumns('payment_type'), $rows);
    }

    /** @return array old customer id => officer id */
    private function officers(array $entities, array $departments): array
    {
        $ids = DB::table('officer')->pluck('id', 'identity_member_no')->all();
        $next = self::nextId('officer');
        $members = [];
        $rows = [];
        foreach ($this->dump->rows('customer_customer') as $row) {
            $code = (string) $row['cus_code'];
            $members[$row['id']] = $ids[$code] ??= $next++;
            $rows[] = ['id' => $members[$row['id']]]
                + $this->dump->officer($row, $entities[$row['business_unit']] ?? null, $departments[$row['operating_unit']] ?? null);
        }
        $this->write('04_officer', 'officer', ImportController::templateColumns('officer'), $rows);

        return $members;
    }

    private function receipts(array $members): void
    {
        $per = max(1, (int) $this->option('rows'));
        $payableColumns = ImportController::templateColumns('officer_payable');
        $paymentColumns = ImportController::templateColumns('officer_payment');
        $payableId = self::nextId('officer_payable');
        $paymentId = self::nextId('officer_payment');
        $payables = [];
        $payments = [];
        $part = 0;
        $flush = function () use (&$payables, &$payments, &$part, $payableColumns, $paymentColumns) {
            $part++;
            $this->write(sprintf('05_officer_payable_%02d', $part), 'officer_payable', $payableColumns, $payables);
            $this->write(sprintf('06_officer_payment_%02d', $part), 'officer_payment', $paymentColumns, $payments);
            $payables = $payments = [];
        };

        foreach ($this->dump->rows('customer_receive') as $row) {
            $officer = $members[$row['customer_id']] ?? null;
            if (! $officer) {
                $this->warn("  Receipt {$row['receipt_id']}: its member is not in the dump, left out.");

                continue;
            }
            $id = $payableId++;
            $payables[] = ['id' => $id] + $this->dump->payable($row, $officer);
            if ($payment = $this->dump->payment($row, $officer, $id)) {
                $payments[] = ['id' => $paymentId++] + $payment;
            }
            if (count($payables) >= $per) {
                $flush();
            }
        }
        if ($payables) {
            $flush();
        }
    }

    private function incentives(array $members): void
    {
        $next = self::nextId('officer_incentive');
        $rows = [];
        foreach ($this->dump->rows('customer_payment') as $row) {
            if ($officer = $members[$row['customer_id']] ?? null) {
                $rows[] = ['id' => $next++] + $this->dump->incentive($row, $officer);
            } else {
                $this->warn("  Benefit {$row['payment_id']}: its member is not in the dump, left out.");
            }
        }
        $this->write('07_officer_incentive', 'officer_incentive', ImportController::templateColumns('officer_incentive'), $rows);
    }

    /** The first id after the table's rows: new rows in the files get ids from here */
    private static function nextId(string $table): int
    {
        return (int) DB::table($table)->max('id') + 1;
    }

    /** Columns of a reading sheet (00_*): all text */
    private static function textColumns(string ...$names): array
    {
        return array_fill_keys($names, ['type' => 'text']);
    }

    /** One workbook in the template format (TemplateWorkbook) */
    private function write(string $file, string $sheetTitle, array $columns, array $rows): void
    {
        TemplateWorkbook::write("{$this->out}/{$file}.xlsx", $sheetTitle, $columns, $rows);
        $this->line(sprintf('  <info>ok</info>    %-28s %6d rows', "{$file}.xlsx", count($rows)));
    }
}
