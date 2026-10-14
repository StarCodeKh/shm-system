<?php

namespace App\Services;

use App\Models\Settings\Currency;
use App\Models\Settings\Status;
use Carbon\Carbon;

/**
 * The old SHM system (Django, PostgreSQL): reads its pg_dump file and maps its rows to this system's tables.
 * Used by db:import-shm (straight into the database) and db:export-shm (Excel files for the list imports),
 * so both give the same data.
 *
 *   customer_customer   -> officer (+ officer_document for the scanned file)
 *   customer_receive    -> officer_payable, + officer_payment when the receipt was not cancelled
 *   customer_payment    -> officer_incentive (benefit paid, titled by its customer_paymenttype)
 *
 * The rows below hold every mapped column; ids, created_by and the marking code are added by the caller.
 */
class ShmLegacy
{
    const GENDERS = ['ប្រុស' => 'male', 'ស្រី' => 'female'];

    private ?array $types = null;

    private ?array $members = null;

    private ?array $units = null;

    /** What was repaired or left out, one line each (shown by the commands) */
    public array $warnings = [];

    private ?int $khr = null;

    public function __construct(public readonly string $file, private string $mediaUrl = '/images/uploads/legacy')
    {
        $this->mediaUrl = rtrim($mediaUrl, '/');
    }

    public static function isDump(string $file): bool
    {
        return is_readable($file) && str_contains((string) file_get_contents($file, false, null, 0, 200), 'PostgreSQL database dump');
    }

    /**
     * The id of an old operating unit in $departments (code => id), or null when unknown.
     * Some old codes carry an extra zero (HD0001 for HD001): matched on their letters and number.
     */
    public static function departmentId(array $departments, ?string $code): ?int
    {
        if (isset($departments[$code])) {
            return $departments[$code];
        }
        $key = fn ($code) => preg_match('/^(\D+)(\d+)$/', (string) $code, $m) ? $m[1].(int) $m[2] : null;
        if ($key($code) === null) {
            return null;
        }
        foreach ($departments as $known => $id) {
            if ($key($known) === $key($code)) {
                return $id;
            }
        }

        return null;
    }

    /** The id of the unit in $entities (code => id) whose code a department code starts with (the longest one), or null */
    public static function entityId(array $entities, ?string $departmentCode): ?int
    {
        $found = null;
        foreach ($entities as $code => $id) {
            $code = (string) $code;
            if ($code !== '' && str_starts_with((string) $departmentCode, $code) && ($found === null || strlen($code) > strlen($found))) {
                $found = $code;
            }
        }

        return $found === null ? null : $entities[$found];
    }

    /** A member; $entityId / $departmentId: the ids of its old business / operating unit (null when unknown) */
    public function officer(array $row, ?int $entityId, ?int $departmentId): array
    {
        if ($entityId === null || $departmentId === null) {
            $this->warnings[] = "Member {$row['cus_code']}: unit {$row['business_unit']} / {$row['operating_unit']} is not in the old unit list, left empty.";
        }
        $first = self::clean($row['first_name']);
        $last = self::clean($row['last_name']);
        $dob = $row['birth_date'];
        if ($dob && substr($dob, 0, 4) < '1900') {
            $this->warnings[] = "Member {$row['cus_code']}: birth date {$dob} is not a real date, left empty.";
            $dob = null;
        }
        // The old full name, when it was typed differently from first + last name
        $full = self::clean($row['username']);
        $remark = $full !== '' && preg_replace('/\s+/u', '', $full) !== preg_replace('/\s+/u', '', $first.$last)
            ? "ឈ្មោះពេញក្នុងប្រព័ន្ធចាស់៖ {$full}" : null;

        return [
            'photo' => $row['img'] ? "{$this->mediaUrl}/{$row['img']}" : null,
            'first_name' => $first,
            'last_name' => $last,
            'gender' => self::GENDERS[$row['gender']] ?? null,
            'dob' => $dob,
            'identity_member_no' => (string) $row['cus_code'],
            'register_date' => $row['register_date'],
            'entity_id' => $entityId,
            'department_id' => $departmentId,
            'telephone_1' => trim($row['phone']) ?: null,
            'is_nssf_member' => null, // not recorded in the old system
            'status' => self::memberStatus($row),
            'remark' => $remark,
            'created_at' => $row['created_date'],
            'updated_at' => $row['modified_on'],
        ];
    }

    /** The member's scanned file, as a URL path, or null */
    public function document(array $row): ?string
    {
        return $row['document'] ? "{$this->mediaUrl}/{$row['document']}" : null;
    }

    public static function memberStatus(array $row): int
    {
        return match (true) {
            $row['is_dead'] === 't' => Status::DEAD,
            $row['is_retire'] === 't' => Status::RETIRED,
            $row['is_active'] !== 't' => Status::INACTIVE,
            default => Status::ACTIVE,
        };
    }

    /** One receipt line: the year (or registration fee) it pays for. A cancelled receipt is Void. */
    public function payable(array $row, int $officerId): array
    {
        $paidAt = $this->paidAt($row['receive_date'], $row['entry_date'], "Receipt {$row['receipt_id']}");
        $enteredAt = $this->dateTime($row['entry_date']);
        $register = $row['is_register'] === 't';
        $remark = $register ? "ថ្លៃចុះឈ្មោះសមាជិក ឆ្នាំ{$row['year']}" : "វិភាគទានឆ្នាំ{$row['year']} ខែ{$row['month_remark']}";
        if (! $register && $row['month'] !== '12') {
            $remark .= " ({$row['month']} ខែ)";
        }
        $remark .= $this->unitThen($row, 'bu', 'ou');

        return [
            'officer_id' => $officerId,
            'amount' => $row['price'],
            'currency_id' => $this->khr(),
            'payment_term' => $register ? 'Register' : 'Yearly',
            'payment_date' => substr($paidAt, 0, 10),
            'due_date' => "{$row['year']}-01-01",
            'status' => $row['is_cancel'] === 't' ? Status::VOID : Status::PAID,
            'remark' => $remark,
            'created_at' => $enteredAt,
            'updated_at' => $enteredAt,
        ];
    }

    /** The money received for a receipt line, or null when the receipt was cancelled (never money received) */
    public function payment(array $row, int $officerId, int $payableId): ?array
    {
        if ($row['is_cancel'] === 't') {
            return null;
        }
        $paidAt = $this->paidAt($row['receive_date'], $row['entry_date']);
        $enteredAt = $this->dateTime($row['entry_date']);

        return [
            'officer_id' => $officerId,
            'ref_payable_id' => $payableId,
            'receipt_no' => $row['receipt_id'],
            'amount' => $row['price'],
            'payment_date' => $paidAt,
            'issue_date' => $paidAt,
            'currency_id' => $this->khr(),
            'note' => "ទទួលដោយ {$row['receive_by']}",
            'status' => Status::PAID,
            'created_at' => $enteredAt,
            'updated_at' => $enteredAt,
        ];
    }

    /** A benefit paid to a member */
    public function incentive(array $row, int $officerId): array
    {
        $this->types ??= collect($this->rows('customer_paymenttype'))->mapWithKeys(fn ($type) => [$type['id'] => trim($type['description'])])->all();
        $paidAt = $this->paidAt($row['payment_date'], $row['entry_date'], "Benefit {$row['payment_id']}");
        $enteredAt = $this->dateTime($row['entry_date']);
        $note = self::clean($row['description']);
        $remark = ($note !== '' ? "{$note} " : '')."ឧបត្ថម្ភដោយ {$row['record_by']}".$this->unitThen($row, 'bu', 'ou');

        return [
            'officer_id' => $officerId,
            'request_title' => $this->types[$row['payment_type']] ?? 'ការឧបត្ថម្ភ',
            'amount' => $row['amount'],
            'currency_id' => $this->khr(),
            'request_date' => $paidAt,
            'issue_date' => $paidAt,
            'reference' => $row['payment_id'],
            'tax' => 0,
            'amount_after_tax' => $row['amount'],
            'is_notify' => false,
            'status' => $row['is_active'] === 't' ? Status::COMPLETED : Status::DELETED,
            'remark' => $remark,
            'created_at' => $enteredAt,
            'updated_at' => $enteredAt,
        ];
    }

    /** Every table in the dump with its columns: table => [column, ...] */
    public function tables(): array
    {
        $tables = [];
        $handle = fopen($this->file, 'r');
        while (($line = fgets($handle)) !== false) {
            if (preg_match('/^COPY public\.(\w+) \((.*)\) FROM stdin;/', $line, $m)) {
                $tables[$m[1]] = array_map(fn ($name) => trim($name, ' "'), explode(',', $m[2]));
            }
        }
        fclose($handle);

        return $tables;
    }

    /** Rows of one table's COPY block, as column => value (NULL as null, booleans stay 't' / 'f') */
    public function rows(string $table): \Generator
    {
        $handle = fopen($this->file, 'r');
        $columns = null;
        try {
            while (($line = fgets($handle)) !== false) {
                if ($columns === null) {
                    if (str_starts_with($line, "COPY public.{$table} (")) {
                        $list = substr($line, strpos($line, '(') + 1, strpos($line, ')') - strpos($line, '(') - 1);
                        $columns = array_map(fn ($name) => trim($name, ' "'), explode(',', $list));
                    }

                    continue;
                }
                $line = rtrim($line, "\r\n");
                if ($line === '\\.') {
                    return;
                }
                yield array_combine($columns, array_map(fn ($value) => $this->value($value), explode("\t", $line)));
            }
        } finally {
            fclose($handle);
        }
        if ($columns === null) {
            throw new \RuntimeException("Table {$table} is not in the dump.");
        }
    }

    /** One value of PostgreSQL's COPY text format */
    private function value(string $value): ?string
    {
        if ($value === '\\N') {
            return null;
        }
        if (! str_contains($value, '\\')) {
            return $value;
        }

        return preg_replace_callback('/\\\\([0-7]{1,3}|x[0-9a-fA-F]{1,2}|.)/', function ($m) {
            $code = $m[1];
            if ($code[0] === 'x' && strlen($code) > 1) {
                return chr(hexdec(substr($code, 1)));
            }
            if (ctype_digit($code)) {
                return chr(octdec($code));
            }

            return ['b' => "\x08", 'f' => "\f", 'n' => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v"][$code] ?? $code;
        }, $value);
    }

    /**
     * When money was paid. A year typed wrong takes the year it was entered, keeping the day and month:
     * an impossible year (0023-02-03, 0204-05-06), or one more than a year after it was entered (2029 for 2023).
     * $label: report the repair as that record.
     */
    private function paidAt(?string $value, ?string $entered, ?string $label = null): ?string
    {
        $wrong = $value && $entered && (substr($value, 0, 4) < '1900'
            || Carbon::parse(substr($value, 0, 10))->gt(Carbon::parse(substr($entered, 0, 10))->addYear()));
        if ($wrong) {
            $fixed = substr($entered, 0, 4).substr($value, 4);
            if ($label) {
                $this->warnings[] = "{$label}: date ".substr($value, 0, 10).' read as '.substr($fixed, 0, 10).' (year of entry).';
            }
            $value = $fixed;
        }

        return $this->dateTime($value);
    }

    /** " · នៅ {department}" when the record was made in another unit than the member's unit now, else "" */
    private function unitThen(array $row, string $bu, string $ou): string
    {
        $this->members ??= collect($this->rows('customer_customer'))->mapWithKeys(fn ($m) => [$m['id'] => $m['business_unit'].'/'.$m['operating_unit']])->all();
        $this->units ??= collect($this->rows('master_data_operatingunit'))->mapWithKeys(fn ($u) => [$u['operating_unit'] => trim($u['description'])])->all();
        if (! $row[$ou] || ($this->members[$row['customer_id']] ?? null) === $row[$bu].'/'.$row[$ou]) {
            return '';
        }

        return ' · នៅ '.($this->units[$row[$ou]] ?? $row[$ou])." ({$row[$ou]})";
    }

    /** Trimmed, without the invisible zero-width characters some old names carry */
    private static function clean(?string $value): string
    {
        return trim(preg_replace(['/[\x{200B}\x{200C}\x{FEFF}]/u', '/\s+/u'], ['', ' '], (string) $value));
    }

    /**
     * "2025-06-12 10:19:22.69+00" -> local time, as the app stores it.
     * A plain date (typed by staff, stored as midnight UTC) stays that date at 00:00, not 07:00.
     */
    private function dateTime(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        if (preg_match('/^(\d{4}-\d{2}-\d{2}) 00:00:00(\+00)?$/', $value, $m)) {
            return "{$m[1]} 00:00:00";
        }

        return Carbon::parse($value)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    private function khr(): ?int
    {
        return $this->khr ??= Currency::idOf(Currency::KHR);
    }
}
