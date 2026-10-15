<?php

namespace App\Models;

use App\Models\Concerns\HasDataScope;
use App\Models\Settings\Account;
use App\Models\Settings\Currency;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\Status;
use App\Services\DataScope;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Income extends Model implements Auditable
{
    use HasDataScope, \OwenIt\Auditing\Auditable;

    protected $table = 'income';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    protected $casts = [
        'issue_date' => 'datetime:'.('Y-m-d'),
    ];

    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new Income)->getTable());
        $str = '';
        for ($i = 0; $i < count($data['tables']); $i++) {
            if ($data['tables'][$i]->Null == 'NO' && $data['tables'][$i]->Field != 'id') {
                $str .= $data['tables'][$i]->Field.',';
            }
        }
        $newArr = explode(',', $str);
        array_pop($newArr);
        $dataFields = $newArr;

        return $dataFields;
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public static function formAttributes()
    {
        $formAttributes = [];
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new Income)->getTable());
        for ($i = 0; $i < count($data['tables']); $i++) {
            if ($data['tables'][$i]->Field != 'id' && $data['tables'][$i]->Field != 'created_by' && $data['tables'][$i]->Field != 'modified_by' && $data['tables'][$i]->Field != 'created_at' && $data['tables'][$i]->Field != 'updated_at') {
                $formAttributes[] = [
                    'name' => $data['tables'][$i]->Field,
                    'type' => CommonService::convertDataTypeWay1($data['tables'][$i]),
                    'required' => $data['tables'][$i]->Null == 'NO' ? true : false,
                ];
            }
        }

        return $formAttributes;
    }

    public static function getSingleOpt()
    {
        $query = Income::orderByLevel()->whereNotDeleted()->get();
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
        $query = Income::with('department')->with('entity')->with('currency')->with('account')->with('status')->with('PaymentMethod');
        $whereClause = $query;
        $whereClause->orderByFilter($filter);
        $d = '';
        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (isset($filter['search_field'])) {
                for ($i = 0; $i < count($filter['search_field']); $i++) {
                    $field = array_key_first($filter['search_field'][$i]);
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

    public static function getData($filter)
    {
        $whereClause = Income::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = Income::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }

    public static function getMonthlyIncome()
    {
        $results = DB::table('income')
            ->tap(fn ($query) => DataScope::where($query, 'income'))
            ->select('issue_date', DB::raw('SUM(amount) as total_amount'))
            ->whereYear('issue_date', date('Y'))
            ->groupBy(
                DB::raw('YEAR(issue_date)'),
                DB::raw('MONTH(issue_date)')
            )
            ->orderBy('issue_date')
            ->orderBy('issue_date')
            ->get();
        $data = [];
        foreach ($results as $row) {
            $data[] = $row->total_amount;
        }

        return $data;
    }

    public static function insertIncome($data)
    {
        $sql = '
            INSERT INTO tbl_income
            (
                title_en,
                title_kh,
                department_id,
                entity_id, 
                account_code, 
                issue_date,
                category, 
                payment_method_id, 
                amount, 
                exchange_rate, 
                remark, 
                reference, 
                currency_id, 
                status, 
                created_by, 
                created_at
            )
            VALUES (
                :title_en,
                :title_kh,
                :department_id,
                :entity_id,
                :account_code,
                NOW(),
                :category,
                :payment_method_id,
                :amount,
                :exchange_rate,
                :remark,
                :reference,
                :currency_id,
                :status,
                :created_by,
                NOW()
            )
        ';

        return DB::insert($sql, [
            'title_en' => $data['title_en'],
            'title_kh' => $data['title_kh'],
            'department_id' => $data['department_id'],
            'entity_id' => $data['entity_id'],
            'account_code' => $data['account_code'],
            'category' => $data['category'],
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'exchange_rate' => $data['exchange_rate'],
            'remark' => $data['remark'] ?? null,
            'reference' => $data['reference'] ?? null,
            'currency_id' => $data['currency_id'],
            'status' => $data['status'],
            'created_by' => $data['created_by'],
        ]);
    }
}
