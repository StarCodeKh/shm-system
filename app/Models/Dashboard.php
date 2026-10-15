<?php

namespace App\Models;

use App\Models\Settings\Currency;
use App\Models\Settings\Status;
use App\Services\DataScope;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Dashboard extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'account';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    /**
     * A table for the raw SQL below: the name with the configured prefix (DB_PREFIX), plus $alias.
     * Tables under a data scope (App\Services\DataScope) come back as a derived table holding only
     * the rows the signed-in user's role may see, so every total follows the role.
     */
    protected static function table(string $name, ?string $alias = null): string
    {
        $table = DB::getTablePrefix().$name;
        $condition = DataScope::sql($name, 'scoped');
        if ($condition === '1 = 1') {
            return $alias ? "$table $alias" : $table;
        }

        return "(SELECT * FROM $table scoped WHERE $condition) ".($alias ?: $table);
    }

    public static function getOfficerGrowth()
    {
        $query = '
            SELECT 
                YEAR(register_date) AS year,
                COUNT(*) AS total_officers,
                ROUND(
                    (
                        (COUNT(*) - LAG(COUNT(*)) OVER (ORDER BY YEAR(register_date))) /
                        NULLIF(LAG(COUNT(*)) OVER (ORDER BY YEAR(register_date)), 0)
                    ) * 100,
                    0
                ) AS growth_rate
            FROM '.self::table('officer').'
            WHERE YEAR(register_date) >= YEAR(CURDATE()) - 5
            GROUP BY YEAR(register_date)
            ORDER BY YEAR(register_date)
        ';

        return collect(DB::select($query))->map(function ($item) {
            return [
                'year' => (int) $item->year,
                'total_officers' => (int) $item->total_officers,
                'growth_rate' => (float) $item->growth_rate ?? null,
            ];
        });
    }

    public static function getTotalExpenseKHR($start_date = null, $end_date = null)
    {
        // Auto default date handling
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }
        $result = DB::select('
            SELECT 
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS total_in_khr,
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS total_in_usd
            FROM (
                SELECT amount, currency_id AS currency_id FROM '.self::table('expense').'
                WHERE issue_date BETWEEN ? AND ?
                UNION ALL
                SELECT amount, currency_id FROM '.self::table('officer_incentive').'
                WHERE issue_date BETWEEN ? AND ? AND status <> '.Status::DELETED.' -- deleted benefits are not money paid
            ) AS total_expense_khr
        ', [
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
        ]);

        return $result[0] ?? (object) [
            'total_in_khr' => 0,
            'total_in_usd' => 0,
        ];
    }

    public static function getRequestInfo()
    {
        $result = DB::select('
            SELECT 
            (
                SELECT COUNT(*) FROM '.self::table('officer_request')." WHERE status = '11'
            ) AS pending_request,
            (
            SELECT COUNT(*) FROM ".self::table('officer_request')." WHERE status = '3'
            ) AS completed_request
        ");

        return $result[0] ?? (object) [
            'pending_request' => 0,
            'completed_request' => 0,
        ];
    }

    public static function getTotalIncomeKHR($start_date = null, $end_date = null)
    {
        // Auto default date handling
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }
        $result = DB::select('
            SELECT 
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS total_in_khr,
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS total_in_usd
            FROM (
                SELECT amount, currency_id as currency_id FROM '.self::table('income').'
                WHERE issue_date BETWEEN ? AND ?
                UNION ALL
                SELECT amount, currency_id FROM '.self::table('officer_payment').'
                WHERE issue_date BETWEEN ? AND ?
            ) AS total_income_khr
        ', [
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
        ]);

        return $result[0] ?? (object) [
            'total_in_khr' => 0,
            'total_in_usd' => 0,
        ];

        return $results[0] ?? null;
    }

    public static function getTotalRemainingBalanceKHR($start_date = null, $end_date = null)
    {
        // Auto default date handling
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }

        $result = DB::select('
            SELECT
                COALESCE(A.khr,0) - COALESCE(B.khr,0) AS remaining_balance_khr,
                COALESCE(A.usd,0) - COALESCE(B.usd,0) AS remaining_balance_usd
            FROM
            (
                -- income totals by currency
                SELECT
                    SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS khr,
                    SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS usd
                FROM (
                    SELECT amount, currency_id FROM '.self::table('income').'
                    WHERE issue_date BETWEEN ? AND ?

                    UNION ALL

                    SELECT amount, currency_id FROM '.self::table('officer_payment').'
                    WHERE issue_date BETWEEN ? AND ?
                ) AS income_all
            ) AS A
            CROSS JOIN
            (
                -- expense totals by currency
                SELECT
                    SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS khr,
                    SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS usd
                FROM (
                    SELECT amount, currency_id FROM '.self::table('expense').'
                    WHERE issue_date BETWEEN ? AND ?

                    UNION ALL

                    SELECT amount, currency_id FROM '.self::table('officer_incentive').'
                    WHERE issue_date BETWEEN ? AND ? AND status <> '.Status::DELETED.' -- deleted benefits are not money paid
                ) AS expense_all
            ) AS B
        ', [
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
            $start_date.' 00:00:00',
            $end_date.' 23:59:59',
        ]);

        return $result[0] ?? (object) [
            'remaining_balance_khr' => 0,
            'remaining_balance_usd' => 0,
        ];
    }

    /**
     * Income per institution (entity) in the period, in riel, largest first: member payments (by the member's
     * institution) plus income records. Each role sees only its own scope (DataScope).
     * Returns ['incomes' => [amount, ...], 'departments' => [institution name, ...]] (the keys the dashboard reads).
     */
    public static function getIncomeByEntity($start_date = null, $end_date = null): array
    {
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }
        $range = [$start_date.' 00:00:00', $end_date.' 23:59:59'];
        $khr = Currency::idOf(Currency::KHR);
        $prefix = DB::getTablePrefix(); // aliases are prefixed too (tbl_p) in raw SQL

        $payments = DB::table('officer_payment as p')->join('officer as o', 'o.id', '=', 'p.officer_id')
            ->where('p.currency_id', $khr)->whereBetween('p.issue_date', $range)
            ->tap(fn ($query) => DataScope::where($query, 'officer_payment', 'p'))
            ->groupBy('o.entity_id')->select('o.entity_id')->selectRaw("SUM({$prefix}p.amount) AS amount")->pluck('amount', 'entity_id');
        $incomes = DB::table('income as i')
            ->where('i.currency_id', $khr)->whereBetween('i.issue_date', $range)
            ->tap(fn ($query) => DataScope::where($query, 'income', 'i'))
            ->groupBy('i.entity_id')->select('i.entity_id')->selectRaw("SUM({$prefix}i.amount) AS amount")->pluck('amount', 'entity_id');

        $totals = [];
        foreach ([$payments, $incomes] as $list) {
            foreach ($list as $entity => $amount) {
                $totals[$entity] = ($totals[$entity] ?? 0) + (float) $amount;
            }
        }
        arsort($totals);
        $names = DB::table('entity')->whereIn('id', array_keys($totals))->get(['id', 'name_kh', 'name_en'])->keyBy('id');

        return [
            'ids' => array_map(fn ($id) => $id === '' ? null : (int) $id, array_keys($totals)), // institution of each bar: the list filter (ListLink)
            'incomes' => array_values($totals),
            'departments' => array_map(fn ($id) => isset($names[$id]) ? CommonService::localName($names[$id]) : '-', array_keys($totals)),
        ];
    }

    /** Benefits paid in the period, by benefit type (matched on the type's Khmer name, which every benefit stores as its title) */
    public static function getTotalIncentiveByCategory($start_date = null, $end_date = null)
    {
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }
        $result = '
            SELECT
                B.id AS payment_type_id,
                B.name_kh,
                B.name_en,
                -- Total KHR
                SUM(
                    CASE
                        WHEN A.currency_id = '.Currency::idOf(Currency::KHR).' THEN A.amount
                        ELSE 0
                    END
                ) AS total_amount_khr,
                -- Total USD
                SUM(
                    CASE
                        WHEN A.currency_id = '.Currency::idOf(Currency::USD).' THEN A.amount
                        ELSE 0
                    END
                ) AS total_amount_usd
            FROM
                '.self::table('officer_incentive', 'A').' 
            INNER JOIN
                '.self::table('payment_type', 'B').' ON A.request_title = B.name_kh
            WHERE
                A.issue_date BETWEEN ? AND ?
                AND A.status <> '.Status::DELETED.'
            GROUP BY
                B.id, B.name_kh, B.name_en
            ORDER BY
                B.id
        ';

        return collect(DB::select($result, [$start_date.' 00:00:00', $end_date.' 23:59:59']))->map(function ($item) {
            return [
                'payment_type_id' => (int) $item->payment_type_id,
                'payment_type_name' => CommonService::localName($item),
                'payment_type_key' => $item->name_kh, // what benefits store as their title: the list filter (ListLink)
                'total_amount_khr' => (float) $item->total_amount_khr,
                'total_amount_usd' => (float) $item->total_amount_usd,
            ];
        });
    }

    /**
     * @param  int|null  $year  The year to report on. Defaults to current year.
     * @param  int|null  $quarter  Specific quarter (1-4) to filter by, or null for all.
     * @return array
     */
    public static function getQuarterlyIncomeExpense($year = null, $quarter = null)
    {
        // Default to current year if not provided
        $year = $year ?? date('Y');

        // Build SQL with positional '?' parameters
        $sql = "
        SELECT
            Q.Q AS quarter,
            CONCAT('Q', Q.Q) AS quarter_label,
            -- Income totals
            COALESCE(SUM(d.income_khr), 0) AS total_income_khr,
            COALESCE(SUM(d.income_usd), 0) AS total_income_usd,
            -- Expense totals
            COALESCE(SUM(d.expense_khr), 0) AS total_expense_khr,
            COALESCE(SUM(d.expense_usd), 0) AS total_expense_usd,
            -- Remaining balances (income - expense) per currency
            COALESCE(SUM(d.income_khr), 0) - COALESCE(SUM(d.expense_khr), 0) AS remaining_balance_khr,
            COALESCE(SUM(d.income_usd), 0) - COALESCE(SUM(d.expense_usd), 0) AS remaining_balance_usd
        FROM
            (
                -- Generate all 4 quarters
                SELECT 1 AS Q UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4
            ) AS Q
        LEFT JOIN
            (
                -- Unified data per record (no conversion), by the date the money moved (issue_date), like the
                -- dashboard cards and monthly chart; created_at is only when it was typed in
                SELECT
                    QUARTER(issue_date) AS quarter,
                    YEAR(issue_date) AS year,
                    -- Income separated by currency
                    CASE WHEN src = 'income' AND currency_id = ".Currency::idOf(Currency::KHR)." THEN amount ELSE 0 END AS income_khr,
                    CASE WHEN src = 'income' AND currency_id = ".Currency::idOf(Currency::USD)." THEN amount ELSE 0 END AS income_usd,
                    -- Expense separated by currency
                    CASE WHEN src = 'expense' AND currency_id = ".Currency::idOf(Currency::KHR)." THEN amount ELSE 0 END AS expense_khr,
                    CASE WHEN src = 'expense' AND currency_id = ".Currency::idOf(Currency::USD)." THEN amount ELSE 0 END AS expense_usd
                FROM (
                    -- Combine all data
                    SELECT amount, currency_id AS currency_id, issue_date, 'income' AS src FROM ".self::table('income')."
                    UNION ALL
                    SELECT amount, currency_id, issue_date, 'income' AS src FROM ".self::table('officer_payment')."
                    UNION ALL
                    SELECT amount, currency_id AS currency_id, issue_date, 'expense' AS src FROM ".self::table('expense')."
                    UNION ALL
                    SELECT amount, currency_id, issue_date, 'expense' AS src FROM ".self::table('officer_incentive').' WHERE status <> '.Status::DELETED."
                ) AS all_data
                WHERE YEAR(issue_date) = ? -- Bind to $year (1st param)
            ) AS d
            ON Q.Q = d.quarter AND d.year = ? -- Bind to $year (2nd param)
        WHERE (? IS NULL OR Q.Q = ? ) -- Bind to $quarter (3rd & 4th params)
        GROUP BY Q.Q
        ORDER BY Q.Q
    ";

        $results = DB::select($sql, [
            $year,
            $year,
            $quarter,
            $quarter,
        ]);

        return $results;
    }

    public static function getOfficerPaymentSummary()
    {
        $sql = '
        WITH payments AS (
            SELECT 
                officer_id,
                MAX(CASE WHEN status = '.Status::PAID.' THEN 1 ELSE 0 END) AS has_paid,
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS paid_khr,
                SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS paid_usd
            FROM '.self::table('officer_payment').'
            GROUP BY officer_id
        ),
        payables AS (
            SELECT
                officer_id,
                SUM(CASE WHEN status = '.Status::UNPAID.' AND currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS unpaid_khr,
                SUM(CASE WHEN status = '.Status::UNPAID.' AND currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS unpaid_usd,
                MAX(payment_term) AS payment_term
            FROM '.self::table('officer_payable').'
            GROUP BY officer_id
        )
        SELECT
            COUNT(*) AS total_officers,
            CAST(SUM(COALESCE(p.has_paid,0)) AS UNSIGNED) AS officers_paid,
            CAST(SUM(CASE WHEN COALESCE(p.has_paid,0) = 0 THEN 1 ELSE 0 END) AS UNSIGNED) AS officers_unpaid,
            COALESCE(SUM(p.paid_khr),0) AS total_paid_khr,
            COALESCE(SUM(p.paid_usd),0) AS total_paid_usd,
            COALESCE(SUM(pay.unpaid_khr),0) AS total_unpaid_khr,
            COALESCE(SUM(pay.unpaid_usd),0) AS total_unpaid_usd,
            CAST(
                ROUND(100.0 * SUM(COALESCE(p.has_paid,0)) / NULLIF(COUNT(*),0))
                AS UNSIGNED
            ) AS percent_paid
        FROM '.self::table('officer', 'o').' 
        LEFT JOIN payments p ON o.id = p.officer_id
        LEFT JOIN payables pay ON o.id = pay.officer_id
        where o.status = '.Status::ACTIVE.'
    ';

        // Execute SQL with an empty array, as this query has no parameters.
        $results = DB::select($sql, []);

        return $results[0] ?? (object) [
            'total_officers' => 0,
            'officers_paid' => 0,
            'officers_unpaid' => 0,
            'total_paid_khr' => 0,
            'total_paid_usd' => 0,
            'total_unpaid_khr' => 0,
            'total_unpaid_usd' => 0,
            'percent_paid' => 0,
        ];
    }

    /**
     * Income and expense per month for the date range, split by currency.
     * Uses the same sources as the dashboard totals: income + member payments, expense + incentives.
     */
    public static function getMonthlyTrend($start_date = null, $end_date = null)
    {
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }

        // Deleted benefits are not money paid (same as the expense total and the benefit list)
        $only = [self::table('officer_incentive') => ' AND status <> '.Status::DELETED];
        $sumByMonth = function (array $tables) use ($start_date, $end_date, $only) {
            $union = implode(' UNION ALL ', array_map(
                fn ($table) => "SELECT issue_date, amount, currency_id FROM $table WHERE issue_date BETWEEN ? AND ?".($only[$table] ?? ''),
                $tables
            ));
            $bindings = [];
            foreach ($tables as $table) {
                array_push($bindings, $start_date.' 00:00:00', $end_date.' 23:59:59');
            }

            return collect(DB::select("
                SELECT DATE_FORMAT(issue_date, '%Y-%m') AS month,
                    SUM(CASE WHEN currency_id = ".Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS khr,
                    SUM(CASE WHEN currency_id = '.Currency::idOf(Currency::USD)." THEN amount ELSE 0 END) AS usd
                FROM ($union) AS t
                GROUP BY month
            ", $bindings))->keyBy('month');
        };

        $income = $sumByMonth([self::table('income'), self::table('officer_payment')]);
        $expense = $sumByMonth([self::table('expense'), self::table('officer_incentive')]);

        $months = [];
        $end = new \DateTime($end_date);
        for ($month = new \DateTime(substr($start_date, 0, 7).'-01'); $month <= $end; $month->modify('+1 month')) {
            $months[] = $month->format('Y-m');
        }
        $series = fn ($rows, $currency) => array_map(fn ($month) => (float) ($rows[$month]->$currency ?? 0), $months);

        return [
            'months' => $months,
            'income_khr' => $series($income, 'khr'),
            'income_usd' => $series($income, 'usd'),
            'expense_khr' => $series($expense, 'khr'),
            'expense_usd' => $series($expense, 'usd'),
        ];
    }

    public static function getOfficerPaymentSummaryFilterByDate($start_date = null, $end_date = null)
    {

        // Auto default date handling
        if (empty($start_date) || empty($end_date)) {
            $year = date('Y');
            $start_date = "$year-01-01";
            $end_date = "$year-12-31";
        }

        $sql = '
        WITH payables AS (
            SELECT
                officer_id,
                COUNT(*) AS total_payables_range,
                SUM(CASE WHEN status = '.Status::UNPAID.' THEN 1 ELSE 0 END) AS unpaid_count,
                -- Unpaid amounts
                SUM(CASE WHEN status = '.Status::UNPAID.' AND currency_id = '.Currency::idOf(Currency::KHR).' THEN amount ELSE 0 END) AS unpaid_khr,
                SUM(CASE WHEN status = '.Status::UNPAID.' AND currency_id = '.Currency::idOf(Currency::USD).' THEN amount ELSE 0 END) AS unpaid_usd
            FROM '.self::table('officer_payable').'
            -- Period is by due date: unpaid payables have no payment_date yet.
            WHERE due_date >= ?
            AND due_date <= ?
            GROUP BY officer_id
        ),
        payments AS (
            SELECT 
                p.officer_id,
                -- Only successful payments in the same period
                SUM(CASE WHEN p.status = '.Status::PAID.' AND p.currency_id = '.Currency::idOf(Currency::KHR).' THEN p.amount ELSE 0 END) AS paid_khr,
                SUM(CASE WHEN p.status = '.Status::PAID.' AND p.currency_id = '.Currency::idOf(Currency::USD).' THEN p.amount ELSE 0 END) AS paid_usd
            FROM '.self::table('officer_payment', 'p').' 
            INNER JOIN '.self::table('officer_payable', 'pa').'  
                ON p.ref_payable_id = pa.id
            AND pa.due_date >= ?
            AND pa.due_date <= ?
            GROUP BY p.officer_id
        )
        SELECT
            COUNT(*) AS total_officers,
            CAST(SUM(
                CASE 
                    WHEN pb.total_payables_range > 0 AND pb.unpaid_count = 0 THEN 1
                    ELSE 0
                END
            ) AS UNSIGNED) AS officers_paid,
            CAST(SUM(
                CASE 
                    WHEN pb.total_payables_range > 0 AND pb.unpaid_count > 0 THEN 1
                    ELSE 0
                END
            ) AS UNSIGNED) AS officers_unpaid,
            COALESCE(SUM(pm.paid_khr),0) AS total_paid_khr,
            COALESCE(SUM(pm.paid_usd),0) AS total_paid_usd,
            COALESCE(SUM(pb.unpaid_khr),0) AS total_unpaid_khr,
            COALESCE(SUM(pb.unpaid_usd),0) AS total_unpaid_usd,
            COALESCE(
                    CAST(
                        ROUND(
                            100.0 *
                            SUM(CASE WHEN pb.total_payables_range > 0 AND pb.unpaid_count = 0 THEN 1 ELSE 0 END)
                            /
                            NULLIF(SUM(CASE WHEN pb.total_payables_range > 0 THEN 1 ELSE 0 END),0)
                        ) AS UNSIGNED
                    ),
                    0
                ) AS percent_paid
        FROM '.self::table('officer', 'o').' 
        LEFT JOIN payables pb ON o.id = pb.officer_id
        LEFT JOIN payments pm ON o.id = pm.officer_id
        WHERE o.status = '.Status::ACTIVE.'
    ';
        $results = DB::select($sql, [
            $start_date,
            $end_date,
            $start_date,
            $end_date,
        ]);

        return $results[0] ?? null;
    }

    public static function getTotalOfficer()
    {
        $sql = "
        SELECT
            CAST(COUNT(*) AS SIGNED) AS totalMembers,
            CAST(SUM(CASE WHEN A.gender = 'male' THEN 1 ELSE 0 END) AS SIGNED) AS totalMales,
            CAST(SUM(CASE WHEN A.gender = 'female' THEN 1 ELSE 0 END) AS SIGNED) AS totalFemales,
            CAST(SUM(CASE WHEN A.status = ".Status::ACTIVE.' THEN 1 ELSE 0 END) AS SIGNED) AS activeMembers,
            CAST(SUM(CASE WHEN A.status = 14 THEN 1 ELSE 0 END) AS SIGNED) AS inactiveMembers,
            CAST(SUM(CASE WHEN A.status = 26 THEN 1 ELSE 0 END) AS SIGNED) AS deadMembers,
            CAST(SUM(CASE WHEN A.status = 27 THEN 1 ELSE 0 END) AS SIGNED) AS retiredMembers
        FROM '.self::table('officer', 'A').' 
        INNER JOIN '.self::table('mef_status', 'B')."  
            ON A.status = B.id 
        AND B.`group` IN ('STATUS','OFFICER')
    ";
        $results = DB::select($sql, []);

        return $results[0] ?? (object) [
            'totalMembers' => 0,
            'totalMales' => 0,
            'totalFemales' => 0,
        ];
    }

    public static function getUserDetail($userID = null)
    {
        $sql = '
        SELECT 
            B.identity_member_no                        AS memberId,
            COALESCE(A.`position`, B.`position`)        AS position,
            B.address                                   AS address,
            COALESCE(A.avatar, B.photo)                 AS profile,
            COALESCE(A.telephone_1, B.telephone_1)      AS telephone1,
            COALESCE(A.telephone_2, B.telephone_2)      AS telephone2,
            COALESCE(A.gender, B.gender)                AS gender,
            COALESCE(A.email, B.email)                  AS email
        FROM '.self::table('users', 'A').' 
        LEFT JOIN '.self::table('officer', 'B').' 
            ON A.officer_id = B.id
        WHERE A.id = :userID
    ';
        $result = DB::select($sql, ['userID' => $userID]);

        return $result[0] ?? null;
    }

    /**
     * Fetch paginated notifications for a user
     */
    public static function getNotificationListByUserId($userID = null, $limit = 10, $offset = 0)
    {
        // Ensure integers for safety
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = '
            SELECT 
                notifiable_id   AS userId,
                notifiable_type AS notifiableType,
                `type`          AS type,
                `data`          AS notificationData,
                created_at      AS notificationDateTime,
                read_at         AS readDateTime,
                status          AS status 
            FROM '.self::table('notifications')."
            WHERE notifiable_id = :userID
            ORDER BY created_at DESC
            LIMIT $limit OFFSET $offset
        ";

        return DB::select($sql, ['userID' => $userID]);
    }

    /**
     * Count total notifications for a user
     */
    public static function countNotificationsByUserId($userID = null)
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('notifications').' WHERE notifiable_id = :userID';
        $result = DB::selectOne($sql, ['userID' => $userID]);

        return $result->total ?? 0;
    }

    public static function getIncomeListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = '
        SELECT
            title_kh          AS title,
            amount            AS amount,
            currency_id          AS currency,
            exchange_rate     AS exchangeRate,
            remark            AS remark,
            reference         AS reference,
            issue_date        AS dateTime,
            status            AS status
        FROM '.self::table('income').'
        WHERE status = '.Status::ACTIVE."
        ORDER BY issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'title' => $item->title,
                'amount' => (float) $item->amount,
                'currency' => (int) $item->currency,
                'exchangeRate' => (float) $item->exchangeRate,
                'remark' => $item->remark,
                'reference' => $item->reference,
                'dateTime' => $item->dateTime,
                'status' => (int) $item->status,
            ];
        });
    }

    /**
     * Optional: count total for pagination
     */
    public static function countIncome()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('income').' WHERE status = '.Status::ACTIVE.'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function getExpenseListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = '
        SELECT
            title_kh          AS title,
            amount            AS amount,
            currency_id          AS currency,
            exchange_rate     AS exchangeRate,
            description       AS remark,
            reference         AS reference,
            issue_date        AS dateTime,
            status            AS status
        FROM '.self::table('expense').'
        WHERE status = '.Status::ACTIVE."
        ORDER BY issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'title' => $item->title,
                'amount' => (float) $item->amount,
                'currency' => (int) $item->currency,
                'exchangeRate' => (float) $item->exchangeRate,
                'remark' => $item->remark,
                'reference' => $item->reference,
                'dateTime' => $item->dateTime,
                'status' => (int) $item->status,
            ];
        });
    }

    /**
     * Optional: total count for pagination
     */
    public static function countExpenses()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('expense').' WHERE status = '.Status::ACTIVE.'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function getOfficerPaymentListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = "
        SELECT 
            A.officer_id                               AS officerId,
            CONCAT(B.first_name , ' ', B.last_name)    AS memberFullName,
            A.receipt_no                               AS receiptNo,
            A.payment_date                             AS dateTime,
            A.amount                                   AS amount,
            A.currency_id                              AS currency,
            A.note                                     AS remark,
            A.status                                   AS status
        FROM ".self::table('officer_payment', 'A').' 
        INNER JOIN '.self::table('officer', 'B')." 
            ON A.officer_id = B.id
        ORDER BY A.payment_date DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'officerId' => (int) $item->officerId,
                'memberFullName' => $item->memberFullName,
                'receiptNo' => $item->receiptNo,
                'dateTime' => $item->dateTime,
                'amount' => (float) $item->amount,
                'currency' => (int) $item->currency,
                'remark' => $item->remark,
                'status' => (int) $item->status,
            ];
        });
    }

    /**
     * Optional: total count for pagination
     */
    public static function countOfficerPayments()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('officer_payment').'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function getOfficerIncentiveListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = "
        SELECT 
            A.officer_id                                 AS officerId,
            CONCAT(B.first_name , ' ', B.last_name)      AS memberFullName,
            A.ref_request_id                             AS requestId,
            A.request_title                              AS title,
            A.issue_date                                 AS issueDateTime,
            A.amount                                     AS amount,
            A.currency_id                                AS currency,
            A.remark                                     AS remark
        FROM ".self::table('officer_incentive', 'A').' 
        LEFT JOIN '.self::table('officer', 'B').' 
            ON A.officer_id = B.id
        LEFT JOIN '.self::table('officer_request', 'C')." 
            ON A.ref_request_id = C.id
        ORDER BY A.issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'officerId' => (int) $item->officerId,
                'memberFullName' => $item->memberFullName,
                'requestId' => $item->requestId,
                'title' => $item->title,
                'issueDateTime' => $item->issueDateTime,
                'amount' => (float) $item->amount,
                'currency' => (int) $item->currency,
                'remark' => $item->remark,
            ];
        });
    }

    /**
     * Optional: total count for pagination
     */
    public static function countOfficerIncentives()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('officer_incentive').'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function getOfficerRequestSummary()
    {
        $sql = '
        SELECT 
            S.title_en AS statusNameEn,
            S.title_kh AS statusNameKh,
            COUNT(R.id) AS totalRequests,
            SUM(CASE WHEN R.currency_id = '.Currency::idOf(Currency::KHR).' THEN R.amount ELSE 0 END) AS total_amount_khr,
            SUM(CASE WHEN R.currency_id = '.Currency::idOf(Currency::USD).' THEN R.amount ELSE 0 END) AS total_amount_usd
        FROM '.self::table('officer_request', 'R').' 
        INNER JOIN '.self::table('mef_status', 'S')."  
            ON R.status = S.id
        WHERE S.`group` = 'CEILING'
        GROUP BY S.id, S.title_en, S.title_kh
        ORDER BY S.id
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'statusNameEn' => $item->statusNameEn,
                'statusNameKh' => $item->statusNameKh,
                'totalRequests' => (int) $item->totalRequests,
                'totalAmountKhr' => (float) $item->total_amount_khr,
                'totalAmountUsd' => (float) $item->total_amount_usd,
            ];
        });
    }

    public static function getOfficerRequestListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = "
        SELECT 
            A.officer_id                              AS officerId,
            CONCAT(B.first_name , ' ', B.last_name)   AS memberFullName,
            A.request_title                           AS requestTitle,
            A.amount                                  AS amount,
            A.currency_id                             AS currency,
            A.issue_date                              AS issueDateTime,
            A.request_date                            AS requestDateTime,
            A.remark                                  AS remark,
            A.status                                  AS status
        FROM ".self::table('officer_request', 'A').' 
        LEFT JOIN '.self::table('officer', 'B')." 
            ON A.officer_id = B.id
        ORDER BY A.request_date DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'officerId' => (int) $item->officerId,
                'memberFullName' => $item->memberFullName,
                'title' => $item->requestTitle,
                'amount' => (float) $item->amount,
                'currency' => (int) $item->currency,
                'issueDateTime' => $item->issueDateTime,
                'requestDateTime' => $item->requestDateTime,
                'remark' => $item->remark,
                'status' => $item->status,
            ];
        });
    }

    /**
     * Optional: get total count for pagination
     */
    public static function countOfficerRequests()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('officer_request').'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function getOfficersListing($limit = 10, $offset = 0)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        $sql = "
        SELECT 
            A.id                                      AS officerId,
            CONCAT(A.first_name, ' ', A.last_name)    AS fullName,
            A.latin                                   AS latin,
            A.identity_member_no                      AS identityMemberNo,
            A.dob                                     AS dateOfBirth,
            A.nationality                             AS nationality,
            A.title                                   AS title,
            D.name_kh                                 AS department,
            A.gender                                  AS gender,
            A.address                                 AS address,
            A.email                                   AS email,
            A.photo                                   AS photo
        FROM ".self::table('officer', 'A').' 
        LEFT JOIN '.self::table('department', 'D').'  ON D.id = A.department_id
        WHERE A.status = '.Status::ACTIVE."
        ORDER BY A.id DESC
        LIMIT $limit OFFSET $offset
    ";

        return collect(DB::select($sql))->map(function ($item) {
            return [
                'officerId' => (int) $item->officerId,
                'fullName' => $item->fullName,
                'latin' => $item->latin,
                'identityMemberNo' => $item->identityMemberNo,
                'dob' => $item->dateOfBirth,
                'nationality' => $item->nationality,
                'title' => $item->title,
                'department' => $item->department,
                'gender' => $item->gender,
                'address' => $item->address,
                'email' => $item->email,
                'photo' => $item->photo,
            ];
        });
    }

    public static function countOfficers()
    {
        $sql = 'SELECT COUNT(*) AS total FROM '.self::table('officer').' WHERE status = '.Status::ACTIVE.'';
        $result = DB::selectOne($sql);

        return $result->total ?? 0;
    }

    public static function countOfficersByGender()
    {
        $sql = "
        SELECT 
            SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) AS totalMale,
            SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) AS totalFemale,
            COUNT(*) AS total
        FROM ".self::table('officer').'
        WHERE status = '.Status::ACTIVE.'
    ';

        $result = DB::selectOne($sql);

        return [
            'totalMale' => (int) ($result->totalMale ?? 0),
            'totalFemale' => (int) ($result->totalFemale ?? 0),
            'total' => (int) ($result->total ?? 0),
        ];
    }

    public static function getStatusList($group)
    {
        return DB::table('mef_status')
            ->select(
                'id as id',
                'title_en as name_en',
                'title_kh as name_kh'
            )
            ->where('group', $group)
            ->get();
    }

    public static function getListitles()
    {
        return DB::table('title')
            ->select(
                'id',
                'name_en as title_en',
                'name_kh as title_kh'
            )
            ->get();
    }

    public static function getAllPaymentMethods()
    {
        return DB::table('payment_method')
            ->select(
                'id',
                'name_en',
                'name_kh'
            )
            ->orderByLevel()
            ->orderBy('id')
            ->get();
    }

    public static function getAllAccounts()
    {
        return DB::table('account')
            ->select(
                'code',
                'name_en',
                'name_kh'
            )
            ->orderBy('code')
            ->get();
    }

    public static function getDutyList()
    {
        return DB::table('duty')
            ->select(
                'id',
                'name_en',
                'name_kh'
            )
            ->orderByLevel()
            ->orderBy('id')
            ->get();
    }

    public static function getPositionList()
    {
        return DB::table('position')
            ->select(
                'id',
                'name_en',
                'name_kh'
            )
            ->orderByLevel()
            ->orderBy('id')
            ->get();
    }

    public static function getEntityList()
    {
        return DB::table('entity')
            ->select(
                'id',
                'parent_id',
                'code',
                'name_kh',
                'name_en'
            )
            ->get();
    }
}
