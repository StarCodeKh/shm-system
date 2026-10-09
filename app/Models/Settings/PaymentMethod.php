<?php

namespace App\Models\Settings;

use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class PaymentMethod extends Model implements Auditable
{
    use HasRoles,\OwenIt\Auditing\Auditable;

    protected $table = 'payment_method';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    // public static function
    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new PaymentMethod)->getTable());
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
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new PaymentMethod)->getTable());
        for ($i = 0; $i < count($data['tables']); $i++) {
            if ($data['tables'][$i]->Field != 'id' && $data['tables'][$i]->Field != 'created_by' && $data['tables'][$i]->Field != 'modified_by' && $data['tables'][$i]->Field != 'created_at' && $data['tables'][$i]->Field != 'updated_at') {
                $formAttributes[] = [
                    'name' => $data['tables'][$i]->Field,
                    'type' => CommonService::convertDataType($data['tables'][$i]),
                    'required' => $data['tables'][$i]->Null == 'NO' ? true : false,
                ];
            }
        }

        return $formAttributes;
    }

    public static function getSingleOpt()
    {
        $query = PaymentMethod::orderByLevel()->whereNotDeleted()->get();
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

        $query = PaymentMethod::orderByFilter($filter);
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

    public static function getData($filter)
    {
        $data = [];
        $whereClause = PaymentMethod::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $total = 0;
        $whereClause = PaymentMethod::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }
}
