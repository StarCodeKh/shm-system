<?php

namespace App\Models;

use App\Models\Concerns\HasDataScope;
use App\Models\Concerns\HasListFields;
use App\Models\Settings\Account;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\Status;
use App\Services\DataScope;
use App\Services\ListLink;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class OfficerIncentive extends Model implements Auditable
{
    use HasDataScope, HasListFields, \OwenIt\Auditing\Auditable;

    protected $table = 'officer_incentive';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    // Columns of the list screen, in order (HasListFields)
    const LIST_FIELDS = ['officer_id', 'request_title', 'amount', 'currency_id', 'issue_date', 'status'];

    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerIncentive)->getTable());
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

    public static function formAttributes()
    {
        $formAttributes = [];
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerIncentive)->getTable());
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
        $query = OfficerIncentive::orderByLevel()->whereNotDeleted()->get();
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
        $query = OfficerIncentive::with('officer')->with('status')->with('currency')->with('paymentMethod');
        $whereClause = $query;
        if (isset($filter['data']['officer_id'])) {
            $whereClause->where('officer_id', (int) $filter['data']['officer_id']);
        }
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
                            $whereClause->Where($field, $filter['search_field'][$i][$field]['value']);
                        } else {
                            $whereClause->Where($field, 'Like', '%'.$filter['search_field'][$i][$field].'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotIn('status', [Status::DELETED]);
        ListLink::apply($whereClause, 'officer_incentive', $filter['data']['link'] ?? null); // opened from a dashboard figure

        return $whereClause;
    }

    public static function getData($filter)
    {
        $whereClause = OfficerIncentive::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = OfficerIncentive::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }

    // Report rows before paging: shared by the paged rows and the exact count
    public static function getOfficerReportsQuery($filter)
    {
        $query = DB::table('officer as f')
            ->select('oi.*', 'ms.title_kh as status', 'c.name_kh as currency_id', DB::raw("CONCAT(first_name, ' ', last_name, '-', identity_member_no) AS officer_id"), 'pm.name_kh as payment_method_id')
            ->Join('officer_incentive as oi', 'oi.officer_id', '=', 'f.id')
            ->LeftJoin('payment_method as pm', 'pm.id', '=', 'oi.payment_method_id') // optional: old-system benefits have none
            ->LeftJoin('mef_status as ms', 'ms.id', '=', 'oi.status')
            ->LeftJoin('currency as c', 'c.id', '=', 'oi.currency_id');
        $whereClause = $query;
        DataScope::where($whereClause, 'officer_incentive', 'oi'); // only what the role may see
        if (isset($filter['data']['officer_id'])) {
        }
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
                            $whereClause->Where('oi.'.$field, 'Like', '%'.$filter['search_field'][$i][$field]['value'].'%');
                        } else {
                            $whereClause->Where('oi.'.$field, 'Like', '%'.$filter['search_field'][$i][$field].'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotIn('f.status', [Status::DELETED]);

        return $whereClause;
    }

    public static function getOfficerReports($filter)
    {
        return collect(self::getOfficerReportsQuery($filter)
            ->offset(($filter['page_number'] - 1) * $filter['limit'])
            ->limit($filter['limit'])
            ->get());
    }

    public static function countOfficerReports($filter)
    {
        return self::getOfficerReportsQuery($filter)->count();
    }
}
