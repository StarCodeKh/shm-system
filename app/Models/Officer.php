<?php

namespace App\Models;

use App\Models\Concerns\HasDataScope;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\Status;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Officer extends Model implements Auditable
{
    use HasDataScope,HasRoles, \OwenIt\Auditing\Auditable;

    protected $table = 'officer';

    protected $primaryKey = 'id';

    protected $fillable = [];

    // user_id (the member's login account) is set only by OfficerController::updateIncentiveOfficer
    protected $guarded = ['id', 'user_id'];

    public $timestamps = true;

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function nssf()
    {
        return $this->belongsTo(Status::class, 'is_nssf_member');
    }

    public function title()
    {
        return $this->belongsTo(Title::class, 'title');
    }

    public function payables()
    {
        return $this->hasMany(OfficerPayment::class);
    }

    // public static function
    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
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
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
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
        $query = Officer::orderByLevel()->whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => $row->first_name.$row->last_name.' ('.$row->identity_member_no.')',
                'value' => $row->id,
            ];
        }

        return $data;
    }

    public static function getSingleOptById($param)
    {
        $query = Officer::orderByLevel()->where('id', $param)->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => $row->first_name.$row->last_name.' ('.$row->identity_member_no.')',
                'value' => $row->id,
            ];
        }

        return $data;
    }

    public static function checkQueryResult($filter, $gender)
    {
        $whereClause = '';
        $query = Officer::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->with('status')->with('department')->with('entity')->with('title');
        if (isset($filter['officer_id'])) {
            $whereClause->where('id', $filter['officer_id']);
        }
        if (isset($filter['data']['officer_id'])) {
            $whereClause->where('id', $filter['data']['officer_id']);
        }
        if (isset($gender)) {
            $whereClause->where('gender', $gender);
        }
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

    public static function checkQuery($filter)
    {
        $whereClause = '';
        $query = Officer::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->with('nssf')->with('status')->with('department')->with('entity');
        if (isset($filter['officer_id'])) {
            $whereClause->where('id', $filter['officer_id']);
        }
        if (isset($filter['data']['officer_id'])) {
            $whereClause->where('id', $filter['data']['officer_id'] ? $filter['data']['officer_id'] : $filter['data']['officer_id']['value']);
        }
        if (isset($filter['search_field']) && $filter['search_field']) {
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

        if (isset($filter['keysearch']['keyword']) && $filter['keysearch']['keyword'] != null) {
            $keyword = '%'.$filter['keysearch']['keyword'].'%';
            $whereClause->whereNotIn('status', [Status::DELETED]);
            $whereClause->where(function ($q) use ($keyword) {
                $q->where('first_name', 'Like', $keyword)
                    ->orWhere('last_name', 'Like', $keyword)
                    ->orWhere('identity_member_no', 'Like', $keyword);
            });
        } else {
            $whereClause->whereNotDeleted();
        }

        return $whereClause;
    }

    public static function getData($filter)
    {
        $query = Officer::checkQuery($filter)
            ->offset(($filter['page_number'] - 1) * $filter['limit'])
            ->limit($filter['limit']);

        return $query->get()->map(function ($item) {
            $item->full_name = $item->first_name.' '.$item->last_name;

            return $item;
        });
    }

    public static function getTotal($filter)
    {
        $total = 0;
        $whereClause = Officer::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }

    public static function getTotalOfficerByGender($filter, $gender)
    {
        $total = 0;
        $whereClause = Officer::checkQueryResult($filter, $gender);
        $total = $whereClause->count();

        return $total;
    }

    public static function insertOfficer($data)
    {
        $optional = [
            'nationality', 'duty', 'entity_id', 'department_id', 'gender', 'address', 'email',
            'bank_code', 'bank_account_no', 'updated_bank_acc', 'position', 'telephone_1', 'telephone_2',
            'register_date', 'married_status', 'emergency_person', 'emergency_relative', 'is_nssf_member',
            'nssf_card_no', 'social_media_fb', 'social_media_telegram', 'social_media_whatsapp',
            'social_media_alt', 'remark', 'order_level',
        ];
        $row = [
            'photo' => $data['photo'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'latin' => $data['latin'],
            'identity_no' => $data['identity_no'],
            'identity_member_no' => $data['identity_member_no'],
            'dob' => $data['dob'],
            'title' => $data['title'],
            'status' => $data['status'],
            'bank' => $data['bank'] ?? $data['bank_name'] ?? null,
            'created_by' => $data['created_by'],
            'created_at' => now(),
        ];
        // Optional fields that weren't sent keep the column default
        foreach ($optional as $field) {
            if (array_key_exists($field, $data)) {
                $row[$field] = $data[$field];
            }
        }

        return DB::table('officer')->insert($row);
    }
}
