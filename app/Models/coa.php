<?php

namespace App\Models;

use App\Models\Settings\Status;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class coa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'account';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new coa)->getTable());
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
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new coa)->getTable());
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
        $query = coa::orderByFilter($filter)->whereNotDeleted()->get();
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
        $query = coa::with('status');
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
        $whereClause = coa::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = coa::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }
}
