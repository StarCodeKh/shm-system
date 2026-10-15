<?php

namespace App\Models\Settings;

use CommonService;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    // Accounts contributions are booked on, by payment term (account codes from AccountSeeder)
    const CONTRIBUTIONS = ['Yearly' => '4100', 'Monthly' => '4100', 'Register' => '4200'];

    // Parent of the benefit accounts: one child per benefit type, named like the type
    const BENEFITS = '5000';

    protected $table = 'account';

    protected $primaryKey = 'id';

    protected $fillable = [
        'account_type',
        'parent_code',
        'code',
        'name_en',
        'name_kh',
        'description',
        'order_level',
        'status',
        'created_by',
        'modified_by',
    ];

    public $timestamps = false;

    /** The id of the contribution account for a payment term (CONTRIBUTIONS), or null when it isn't seeded */
    public static function contributionId(?string $paymentTerm): ?int
    {
        static $ids = null;
        $ids ??= self::whereIn('code', array_unique(self::CONTRIBUTIONS))->pluck('id', 'code')->all();
        $code = self::CONTRIBUTIONS[$paymentTerm] ?? null;

        return $code ? ($ids[$code] ?? null) : null;
    }

    public static function getSingleOpt()
    {
        $query = AccountType::whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => CommonService::localName($row),
                'value' => $row->id,
            ];
        }

        return $data;
    }

    public static function checkQuery($filter)
    {
        $whereClause = '';

        $query = Account::orderByFilter($filter);
        $whereClause = $query;
        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (isset($filter['search_field'])) {
                for ($i = 0; $i < count($filter['search_field']); $i++) {
                    $field = array_key_first($filter['search_field'][$i]); // array('key1', 'key2', 'key3');
                    if (array_key_exists($field, $dataFields)) {
                        if (isset($filter['search_field'][$i][$field]['value'])) {
                            $whereClause->Where($field, 'Like', '%'.$filter['search_field'][$i][$field]['value'].'%');
                        } else {
                            $whereClause->Where($field, 'Like', '%'.$filter['search_field'][$i][$field].'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotDeleted();

        return $whereClause;
    }

    public static function getChartAccountParentCode()
    {
        $query = Account::WhereNull('parent_code')->whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => $row->code.':'.$row->name_en,
                'value' => $row->code,
            ];
        }

        return $data;
    }

    public static function getAccountCodeList()
    {
        $query = Account::whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => $row->code.':'.$row->name_en,
                'value' => $row->code,
            ];
        }

        return $data;
    }

    public static function getAccounts($filter)
    {
        $data = [];
        $query = Account::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        if (! empty($filter['search_field'])) {
            $whereClause->offset((1 - 1) * $filter['limit']);
        } else {
            $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        }
        $whereClause->limit($filter['limit']);
        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (array_key_exists('account_type', $dataFields)) {
                $whereClause->Where('account_type', 'Like', '%'.$dataFields['account_type']['value'].'%');
            }
            if (array_key_exists('parent_code', $dataFields)) {
                $whereClause->Where('parent_code', 'Like', '%'.$dataFields['parent_code']['value'].'%');
            }
            if (array_key_exists('code', $dataFields)) {
                $whereClause->Where('code', 'Like', '%'.$dataFields['code'].'%');
            }
            if (array_key_exists('name_kh', $dataFields)) {
                $whereClause->Where('name_kh', 'Like', '%'.$dataFields['name_kh'].'%');
            }
            if (array_key_exists('name_en', $dataFields)) {
                $whereClause->Where('name_en', 'Like', '%'.$dataFields['name_en'].'%');
            }
        }
        $results = collect($whereClause->get());
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'account_type' => $row->account_type,
                'parent_code' => $row->parent_code,
                'code' => $row->code,
                'name_en' => $row->name_en,
                'name_kh' => $row->name_kh,
                'description' => $row->description,
                'order_level' => $row->order_level,
                'status' => $row->status,
                'created_by' => $row->created_by,
                'modified_by' => $row->modified_by,
            ];
        }

        return $data;
    }

    public static function getCount($filter)
    {
        $data = [];
        $query = Account::orderByFilter($filter);
        $whereClause = $query;
        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (array_key_exists('account_type', $dataFields)) {
                $whereClause->Where('account_type', 'Like', '%'.$dataFields['account_type']['value'].'%');
            }
            if (array_key_exists('parent_code', $dataFields)) {
                $whereClause->Where('parent_code', 'Like', '%'.$dataFields['parent_code']['value'].'%');
            }
            if (array_key_exists('code', $dataFields)) {
                $whereClause->Where('code', 'Like', '%'.$dataFields['code'].'%');
            }
            if (array_key_exists('name_kh', $dataFields)) {
                $whereClause->Where('name_kh', 'Like', '%'.$dataFields['name_kh'].'%');
            }
            if (array_key_exists('name_en', $dataFields)) {
                $whereClause->Where('name_en', 'Like', '%'.$dataFields['name_en'].'%');
            }
        }
        $total = $whereClause->count();

        return $total;
    }

    public static function getAccountCode($accountType)
    {
        $query = Account::orderByLevel()->whereIn('account_type', $accountType)->whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => $row->code.': '.$row->name_en,
                'value' => $row->code,
            ];
        }

        return $data;
    }
}
