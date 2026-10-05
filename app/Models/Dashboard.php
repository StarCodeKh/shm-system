<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\CompositionRequest;
use App\Models\Dashboard;
use App\Models\Officer;
use App\Models\Settings\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use DB;
use CommonService;
class Dashboard extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'account';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;

  public static function getOfficerGrowth()
    {
        $query = "
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
            FROM tbl_officer
            WHERE YEAR(register_date) >= YEAR(CURDATE()) - 5
            GROUP BY YEAR(register_date)
            ORDER BY YEAR(register_date)
        ";

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
            $end_date   = "$year-12-31";
        }
        $result = DB::select("
            SELECT 
                SUM(CASE WHEN currency_id = 1 THEN amount ELSE 0 END) AS total_in_khr,
                SUM(CASE WHEN currency_id = 2 THEN amount ELSE 0 END) AS total_in_usd
            FROM (
                SELECT amount, currency_id AS currency_id FROM tbl_expense
                WHERE issue_date BETWEEN ? AND ?
                UNION ALL
                SELECT amount, currency_id FROM tbl_officer_incentive
                WHERE issue_date BETWEEN ? AND ?
            ) AS total_expense_khr
        ", [
            $start_date . ' 00:00:00',
            $end_date   . ' 23:59:59',
            $start_date . ' 00:00:00',
            $end_date   . ' 23:59:59',
        ]);
        return $result[0] ?? (object)[
            'total_in_khr' => 0,
            'total_in_usd' => 0,
        ];
    }


    public static function getRequestInfo()
    {
        $result = DB::select("
            SELECT 
            (
                SELECT COUNT(*) FROM tbl_officer_request WHERE status = '11'
            ) AS pending_request,
            (
            SELECT COUNT(*) FROM tbl_officer_request WHERE status = '3'
            ) AS completed_request
        ");
        return $result[0] ?? (object)[
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
            $end_date   = "$year-12-31";
        }
        $result = DB::select("
            SELECT 
                SUM(CASE WHEN currency_id = 1 THEN amount ELSE 0 END) AS total_in_khr,
                SUM(CASE WHEN currency_id = 2 THEN amount ELSE 0 END) AS total_in_usd
            FROM (
                SELECT amount, currency_id as currency_id FROM tbl_income
                WHERE issue_date BETWEEN ? AND ?
                UNION ALL
                SELECT amount, currency_id FROM tbl_officer_payment
                WHERE issue_date BETWEEN ? AND ?
            ) AS total_income_khr
        ", [
            $start_date . ' 00:00:00',
            $end_date   . ' 23:59:59',
            $start_date . ' 00:00:00',
            $end_date   . ' 23:59:59',
        ]);


        //return $result[0]->total_income_khr ?? 0;
        return $result[0] ?? (object)[
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
            $end_date   = "$year-12-31";
        }

        $result = DB::select("
            SELECT
                COALESCE(A.khr,0) - COALESCE(B.khr,0) AS remaining_balance_khr,
                COALESCE(A.usd,0) - COALESCE(B.usd,0) AS remaining_balance_usd
            FROM
            (
                -- income totals by currency
                SELECT
                    SUM(CASE WHEN currency_id = 1 THEN amount ELSE 0 END) AS khr,
                    SUM(CASE WHEN currency_id = 2 THEN amount ELSE 0 END) AS usd
                FROM (
                    SELECT amount, currency_id FROM tbl_income
                    WHERE issue_date BETWEEN ? AND ?

                    UNION ALL

                    SELECT amount, currency_id FROM tbl_officer_payment
                    WHERE issue_date BETWEEN ? AND ?
                ) AS income_all
            ) AS A
            CROSS JOIN
            (
                -- expense totals by currency
                SELECT
                    SUM(CASE WHEN currency_id = 1 THEN amount ELSE 0 END) AS khr,
                    SUM(CASE WHEN currency_id = 2 THEN amount ELSE 0 END) AS usd
                FROM (
                    SELECT amount, currency_id FROM tbl_expense
                    WHERE issue_date BETWEEN ? AND ?

                    UNION ALL

                    SELECT amount, currency_id FROM tbl_officer_incentive
                    WHERE issue_date BETWEEN ? AND ?
                ) AS expense_all
            ) AS B
        ", [
            $start_date . ' 00:00:00', 
            $end_date   . ' 23:59:59', 
            $start_date . ' 00:00:00', 
            $end_date   . ' 23:59:59', 
            $start_date . ' 00:00:00', 
            $end_date   . ' 23:59:59', 
            $start_date . ' 00:00:00', 
            $end_date   . ' 23:59:59'
        ]);
        // return $result[0]->remaining_balance_khr ?? 0;
        return $result[0] ?? (object)[
            'remaining_balance_khr' => 0,
            'remaining_balance_usd' => 0,
        ];
    }



    public static function getTotalIncentiveByCategory()
    {
        $result = "
            SELECT
                B.id AS payment_type_id,
                B.name_en AS payment_type_name,
                -- Total KHR
                SUM(
                    CASE
                        WHEN A.currency_id = 1 THEN A.amount
                        ELSE 0
                    END
                ) AS total_amount_khr,
                -- Total USD
                SUM(
                    CASE
                        WHEN A.currency_id = 2 THEN A.amount
                        ELSE 0
                    END
                ) AS total_amount_usd
            FROM
                tbl_officer_incentive A
            INNER JOIN
                tbl_payment_type B ON A.request_title = B.name_en -- should join by payment request id (need to add new field)
            GROUP BY
                B.id, B.name_en
            ORDER BY
                B.id
        ";

         return collect(DB::select($result))->map(function ($item) {
            return [
                'payment_type_id' => (int) $item->payment_type_id,
                'payment_type_name' => $item->payment_type_name,
                'total_amount_khr' => (float) $item->total_amount_khr,
                'total_amount_usd' => (float) $item->total_amount_usd,
            ];
        });
    }




/**
 * @param int|null $year The year to report on. Defaults to current year.
 * @param int|null $quarter Specific quarter (1-4) to filter by, or null for all.
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
                -- Unified data per record (no conversion)
                SELECT
                    QUARTER(created_at) AS quarter,
                    YEAR(created_at) AS year,
                    -- Income separated by currency
                    CASE WHEN src = 'income' AND currency_id = 1 THEN amount ELSE 0 END AS income_khr,
                    CASE WHEN src = 'income' AND currency_id = 2 THEN amount ELSE 0 END AS income_usd,
                    -- Expense separated by currency
                    CASE WHEN src = 'expense' AND currency_id = 1 THEN amount ELSE 0 END AS expense_khr,
                    CASE WHEN src = 'expense' AND currency_id = 2 THEN amount ELSE 0 END AS expense_usd
                FROM (
                    -- Combine all data
                    SELECT amount, currency_id AS currency_id, created_at, 'income' AS src FROM tbl_income
                    UNION ALL
                    SELECT amount, currency_id, created_at, 'income' AS src FROM tbl_officer_payment
                    UNION ALL
                    SELECT amount, currency_id AS currency_id, created_at, 'expense' AS src FROM tbl_expense
                    UNION ALL
                    SELECT amount, currency_id, created_at, 'expense' AS src FROM tbl_officer_incentive
                ) AS all_data
                WHERE YEAR(created_at) = ? -- Bind to $year (1st param)
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
        $quarter
    ]);

    return $results;
}


    /**
     * @param int|null $year The year to report on. Defaults to current year.
     * @param int|null $quarter Specific quarter (1-4) to filter by, or null for all.
     * @return array
     */
    public static function getQuarterlyIncomeExpenseWay2($year = null, $quarter = null)
    {
        // Default to current year if not provided
        $year = $year ?? date('Y');

        // Build SQL using named parameters (preferred for CTEs)
        $sql = "
            WITH quarters AS (
                SELECT 1 AS Q UNION ALL
                SELECT 2 UNION ALL
                SELECT 3 UNION ALL
                SELECT 4
            ),
            total_income_expense AS (
                SELECT
                    QUARTER(created_at) AS quarter,
                    YEAR(created_at) AS year,
                    CASE
                        WHEN src = 'income' AND currency_id = 1 THEN amount
                        WHEN src = 'income' AND currency_id = 2 THEN amount * 4000
                        ELSE 0
                    END AS income_khr,
                    CASE
                        WHEN src = 'expense' AND currency_id = 1 THEN amount
                        WHEN src = 'expense' AND currency_id = 2 THEN amount * 4000
                        ELSE 0
                    END AS expense_khr
                FROM (
                    -- Unified source data (UNION ALL)
                    SELECT amount, currency AS currency_id, created_at, 'income' AS src FROM tbl_income
                    UNION ALL
                    SELECT amount, currency_id, created_at, 'income' AS src FROM tbl_officer_payment
                    UNION ALL
                    SELECT amount, currency AS currency_id, created_at, 'expense' AS src FROM tbl_expense
                    UNION ALL
                    SELECT amount, currency_id, created_at, 'expense' AS src FROM tbl_officer_incentive
                ) AS all_data
                WHERE YEAR(created_at) = :year
            )
            SELECT
                Q.Q AS quarter,
                CONCAT('Q', Q.Q) AS quarter_label,
                COALESCE(SUM(d.income_khr), 0) AS total_income_khr,
                COALESCE(SUM(d.expense_khr), 0) AS total_expense_khr,
                COALESCE(SUM(d.income_khr), 0) - COALESCE(SUM(d.expense_khr), 0) AS remaining_balance_khr
            FROM quarters Q
            LEFT JOIN total_income_expense d
                ON Q.Q = d.quarter AND d.year = :year_join
            WHERE (:quarter_filter IS NULL OR Q.Q = :quarter_value)
            GROUP BY Q.Q
            ORDER BY Q.Q;
        ";

        $results = DB::select($sql, [
            'year'          => $year,
            'year_join'     => $year,
            'quarter_filter'=> $quarter,
            'quarter_value' => $quarter
        ]);

        return $results;
    }


public static function getOfficerPaymentSummary()
{
    $sql = "
        WITH payments AS (
            SELECT 
                officer_id,
                MAX(CASE WHEN status = 19 THEN 1 ELSE 0 END) AS has_paid,
                SUM(CASE WHEN currency_id = 1 THEN amount ELSE 0 END) AS paid_khr,
                SUM(CASE WHEN currency_id = 2 THEN amount ELSE 0 END) AS paid_usd
            FROM tbl_officer_payment
            GROUP BY officer_id
        ),
        payables AS (
            SELECT
                officer_id,
                SUM(CASE WHEN status = 15 AND currency_id = 1 THEN amount ELSE 0 END) AS unpaid_khr,
                SUM(CASE WHEN status = 15 AND currency_id = 2 THEN amount ELSE 0 END) AS unpaid_usd,
                MAX(payment_term) AS payment_term
            FROM tbl_officer_payable
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
        FROM tbl_officer o
        LEFT JOIN payments p ON o.id = p.officer_id
        LEFT JOIN payables pay ON o.id = pay.officer_id
        where o.status = '13'
    ";

    // Execute SQL with an empty array, as this query has no parameters.
    $results = DB::select($sql, []);
    // DB::select returns an array of objects, but this query returns a single summary row.
    return $results[0] ?? (object)[
        'total_officers' => 0,
        'officers_paid' => 0,
        'officers_unpaid' => 0,
        'total_paid_khr' => 0,
        'total_paid_usd' => 0,
        'total_unpaid_khr' => 0,
        'total_unpaid_usd' => 0,
        'percent_paid' => 0
    ];
}



public static function getOfficerPaymentSummaryFilterByDate($start_date = null, $end_date = null)
{

    // Auto default date handling
    if (empty($start_date) || empty($end_date)) {
        $year = date('Y');
        $start_date = "$year-01-01";
        $end_date   = "$year-12-31";
    }

    $sql = "
        WITH payables AS (
            SELECT
                officer_id,
                COUNT(*) AS total_payables_range,
                SUM(CASE WHEN status = 15 THEN 1 ELSE 0 END) AS unpaid_count,
                -- Unpaid amounts
                SUM(CASE WHEN status = 15 AND currency_id = 1 THEN amount ELSE 0 END) AS unpaid_khr,
                SUM(CASE WHEN status = 15 AND currency_id = 2 THEN amount ELSE 0 END) AS unpaid_usd
            FROM tbl_officer_payable
            WHERE payment_date >= ?
            AND payment_date <= ?
            GROUP BY officer_id
        ),
        payments AS (
            SELECT 
                p.officer_id,
                -- Only successful payments in the same period
                SUM(CASE WHEN p.status = 19 AND p.currency_id = 1 THEN p.amount ELSE 0 END) AS paid_khr,
                SUM(CASE WHEN p.status = 19 AND p.currency_id = 2 THEN p.amount ELSE 0 END) AS paid_usd
            FROM tbl_officer_payment p
            INNER JOIN tbl_officer_payable pa 
                ON p.ref_payable_id = pa.id
            AND pa.payment_date >= ?
            AND pa.payment_date <= ?
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
        FROM tbl_officer o
        LEFT JOIN payables pb ON o.id = pb.officer_id
        LEFT JOIN payments pm ON o.id = pm.officer_id
        WHERE o.status = '13'
    ";
     $results = DB::select($sql, [
        $start_date,
        $end_date,
        $start_date,
        $end_date
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
            CAST(SUM(CASE WHEN A.status = 13 THEN 1 ELSE 0 END) AS SIGNED) AS activeMembers,
            CAST(SUM(CASE WHEN A.status = 14 THEN 1 ELSE 0 END) AS SIGNED) AS inactiveMembers,
            CAST(SUM(CASE WHEN A.status = 26 THEN 1 ELSE 0 END) AS SIGNED) AS deadMembers,
            CAST(SUM(CASE WHEN A.status = 27 THEN 1 ELSE 0 END) AS SIGNED) AS retiredMembers
        FROM tbl_officer A
        INNER JOIN tbl_mef_status B 
            ON A.status = B.id 
        AND B.`group` IN ('STATUS','OFFICER')
    ";
    $results = DB::select($sql, []);
    return $results[0] ?? (object)[
        'totalMembers' => 0,
        'totalMales' => 0,
        'totalFemales' => 0
    ];
}


public static function getUserDetail($userID = null)
{
    $sql = "
        SELECT 
            B.identity_member_no                        AS memberId,
            COALESCE(A.`position`, B.`position`)        AS position,
            B.address                                   AS address,
            COALESCE(A.avatar, B.photo)                 AS profile,
            COALESCE(A.telephone_1, B.telephone_1)      AS telephone1,
            COALESCE(A.telephone_2, B.telephone_2)      AS telephone2,
            COALESCE(A.gender, B.gender)                AS gender,
            COALESCE(A.email, B.email)                  AS email
        FROM tbl_users A
        LEFT JOIN tbl_officer B
            ON A.officer_id = B.id
        WHERE A.id = :userID
    ";
    $result = DB::select($sql, ['userID' => $userID]);
    return $result[0] ?? null;
}

    /**
     * Fetch paginated notifications for a user
     */
    public static function getNotificationListByUserId($userID = null, $limit = 10, $offset = 0)
    {
        // Ensure integers for safety
        $limit  = (int) $limit;
        $offset = (int) $offset;

        $sql = "
            SELECT 
                notifiable_id   AS userId,
                notifiable_type AS notifiableType,
                `type`          AS type,
                `data`          AS notificationData,
                created_at      AS notificationDateTime,
                read_at         AS readDateTime,
                status          AS status 
            FROM tbl_notifications
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
        $sql = "SELECT COUNT(*) AS total FROM tbl_notifications WHERE notifiable_id = :userID";
        $result = DB::selectOne($sql, ['userID' => $userID]);
        return $result->total ?? 0;
    }



public static function getIncomeListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
    $offset = (int) $offset;

    $sql = "
        SELECT
            income_title      AS title,
            amount            AS amount,
            currency_id          AS currency,
            exchange_rate     AS exchangeRate,
            remark            AS remark,
            reference         AS reference,
            issue_date        AS dateTime,
            status            AS status
        FROM tbl_income
        WHERE status = 13
        ORDER BY issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'title'     => $item->title,
            'amount'          => (float)$item->amount,
            'currency'        => (int)$item->currency,
            'exchangeRate'    => (float)$item->exchangeRate,
            'remark'          => $item->remark,
            'reference'       => $item->reference,
            'dateTime'  => $item->dateTime,
            'status'          => (int)$item->status,
        ];
    });
}

/**
 * Optional: count total for pagination
 */
public static function countIncome()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_income WHERE status = 13";
    $result = DB::selectOne($sql);
    return $result->total ?? 0;
}



public static function getExpenseListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
    $offset = (int) $offset;

    $sql = "
        SELECT
            expense_title     AS title,
            amount            AS amount,
            currency_id          AS currency,
            exchange_rate     AS exchangeRate,
            description       AS remark,
            reference         AS reference,
            issue_date        AS dateTime,
            status            AS status
        FROM tbl_expense
        WHERE status = 13
        ORDER BY issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'title'            => $item->title,
            'amount'           => (float)$item->amount,
            'currency'         => (int)$item->currency,
            'exchangeRate'     => (float)$item->exchangeRate,
            'remark'           => $item->remark,
            'reference'        => $item->reference,
            'dateTime'         => $item->dateTime,
            'status'           => (int)$item->status,
        ];
    });
}

/**
 * Optional: total count for pagination
 */
public static function countExpenses()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_expense WHERE status = 13";
    $result = DB::selectOne($sql);
    return $result->total ?? 0;
}



public static function getOfficerPaymentListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
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
        FROM tbl_officer_payment A
        INNER JOIN tbl_officer B
            ON A.officer_id = B.id
        ORDER BY A.payment_date DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'officerId'       => (int) $item->officerId,
            'memberFullName'  => $item->memberFullName,
            'receiptNo'       => $item->receiptNo,
            'dateTime'        => $item->dateTime,
            'amount'          => (float) $item->amount,
            'currency'        => (int) $item->currency,
            'remark'          => $item->remark,
            'status'          => (int) $item->status
        ];
    });
}

/**
 * Optional: total count for pagination
 */
public static function countOfficerPayments()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_officer_payment";
    $result = DB::selectOne($sql);
    return $result->total ?? 0;
}



public static function getOfficerIncentiveListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
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
        FROM tbl_officer_incentive A
        LEFT JOIN tbl_officer B
            ON A.officer_id = B.id
        LEFT JOIN tbl_officer_request C
            ON A.ref_request_id = C.id
        ORDER BY A.issue_date DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'officerId'       => (int) $item->officerId,
            'memberFullName'  => $item->memberFullName,
            'requestId'       => $item->requestId,
            'title'           => $item->title,
            'issueDateTime'   => $item->issueDateTime,
            'amount'          => (float) $item->amount,
            'currency'        => (int) $item->currency,
            'remark'          => $item->remark
        ];
    });
}

/**
 * Optional: total count for pagination
 */
public static function countOfficerIncentives()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_officer_incentive";
    $result = DB::selectOne($sql);
    return $result->total ?? 0;
}


public static function getOfficerRequestSummary()
{
    $sql = "
        SELECT 
            S.title_en AS statusNameEn,
            S.title_kh AS statusNameKh,
            COUNT(R.id) AS totalRequests,
            SUM(CASE WHEN R.currency_id = 1 THEN R.amount ELSE 0 END) AS total_amount_khr,
            SUM(CASE WHEN R.currency_id = 2 THEN R.amount ELSE 0 END) AS total_amount_usd
        FROM tbl_officer_request R
        INNER JOIN tbl_mef_status S 
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
            'totalAmountUsd' => (float) $item->total_amount_usd
        ];
    });
}


public static function getOfficerRequestListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
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
        FROM tbl_officer_request A
        LEFT JOIN tbl_officer B
            ON A.officer_id = B.id
        ORDER BY A.request_date DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'officerId'       => (int) $item->officerId,
            'memberFullName'  => $item->memberFullName,
            'title'           => $item->requestTitle,
            'amount'          => (float) $item->amount,
            'currency'        => (int) $item->currency,
            'issueDateTime'   => $item->issueDateTime,
            'requestDateTime' => $item->requestDateTime,
            'remark'          => $item->remark,
            'status'          => $item->status,
        ];
    });
}

/**
 * Optional: get total count for pagination
 */
public static function countOfficerRequests()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_officer_request";
    $result = DB::selectOne($sql);
    return $result->total ?? 0;
}

public static function getOfficersListing($limit = 10, $offset = 0)
{
    $limit  = (int) $limit;
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
            A.department                              AS department,
            A.gender                                  AS gender,
            A.address                                 AS address,
            A.email                                   AS email,
            A.photo                                   AS photo
        FROM tbl_officer A
        WHERE A.status = 13
        ORDER BY A.id DESC
        LIMIT $limit OFFSET $offset
    ";

    return collect(DB::select($sql))->map(function ($item) {
        return [
            'officerId'        => (int) $item->officerId,
            'fullName'         => $item->fullName,
            'latin'            => $item->latin,
            'identityMemberNo' => $item->identityMemberNo,
            'dob'              => $item->dateOfBirth,
            'nationality'      => $item->nationality,
            'title'            => $item->title,
            'department'       => $item->department,
            'gender'           => $item->gender,
            'address'          => $item->address,
            'email'            => $item->email,
            'photo'            => $item->photo,
        ];
    });
}

public static function countOfficers()
{
    $sql = "SELECT COUNT(*) AS total FROM tbl_officer WHERE status = 13";
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
        FROM tbl_officer
        WHERE status = 13
    ";

    $result = DB::selectOne($sql);

    return [
        'totalMale'   => (int) ($result->totalMale ?? 0),
        'totalFemale' => (int) ($result->totalFemale ?? 0),
        'total'       => (int) ($result->total ?? 0),
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
        ->get();
}

public static function getEntityList()
{
    return DB::table('entity')
        ->select(
            'id',
            'parent_id',
            'code',
            'department_id',
            'name_kh',
            'name_en'
        )
        ->get();
}

}