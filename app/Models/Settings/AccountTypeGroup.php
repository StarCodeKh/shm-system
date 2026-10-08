<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use CommonService;

class AccountTypeGroup extends Model
{
    protected $table = 'account_type_group';
    protected  $primaryKey = 'id';
    protected $fillable = [
        "name_en",
        "name_kh",
        'order_level',
        'status',
        'created_by',
        'modified_by'
    ];
    public $timestamps = false;

    public static function getAccountTypeGroupOptions($filter)
    {
        $data = array();
        $query = AccountTypeGroup::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        $results = collect($whereClause->get());
        foreach ($results as $row) {
            $data[] = array(
                "label" => CommonService::localName($row),
                "value" => $row->id,
            );
        }
        return $data;
    }

    public static function getAccountTypeGroups($filter)
    {
        $data = array();
        $query = AccountTypeGroup::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);

        if ($filter["search_field"]) {
            // Any of the search fields may match, but the other filters still apply
            $whereClause->where(function ($q) use ($filter) {
                foreach ($filter["search_field"] as $search) {
                    $field = array_key_first($search);
                    $q->orWhere($field, "Like", "%" . $search[$field] . "%");
                }
            });
        }

        $results = collect($whereClause->get());
        foreach ($results as $row) {
            $data[] = array(
                'id' => $row->id,
                'account_type_group' => isset($row->AccountTypeGroup) ? $row->AccountTypeGroup->name_kh : "",
                'account_type_group_id' => array(
                    "label" => $row->AccountTypeGroup ? $row->AccountTypeGroup->name_kh : "",
                    "value" => $row->AccountTypeGroup ? $row->AccountTypeGroup->id : "",
                ),
                "name_en" => $row->name_en ? $row->name_en : "",
                "name_kh" => $row->name_kh ? $row->name_kh : "",
                'description' => $row->description,
                'order_level'
            );
        }
        return $data;
    }

    public static function getCount($filter)
    {
        $data = array();
        $query = AccountTypeGroup::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        if ($filter["search_field"]) {
            // Any of the search fields may match, but the other filters still apply
            $whereClause->where(function ($q) use ($filter) {
                foreach ($filter["search_field"] as $search) {
                    $field = array_key_first($search);
                    $q->orWhere($field, "Like", "%" . $search[$field] . "%");
                }
            });
        }
        $total = $whereClause->count();
        return $total;
    }
}
