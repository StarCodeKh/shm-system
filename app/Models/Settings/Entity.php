<?php

namespace App\Models\Settings;

use Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Entity extends Model implements Auditable
{
    use HasRoles,\OwenIt\Auditing\Auditable;

    protected $table = 'entity';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    public static function getSingleOpt()
    {
        $data = [];
        $query = Entity::whereNotDeleted();
        $whereClause = $query;
        $whereClause->orderByLevel();
        $whereClause->where(function ($q) {
            $q->where('parent_id', 0)->orWhereNull('parent_id');
        });
        if (Auth::user()->entity_id != 0 || Auth::user()->entity_id != null) {
            $whereClause->where('id', Auth::user()->entity_id);
        }
        $queryResult = collect($whereClause->get());
        foreach ($queryResult as $row) {
            $data[] = [
                'label' => $row->code.'-'.$row->name_kh,
                'value' => $row->id,
            ];
        }

        return $data;
    }

    public static function checkQuery($filter)
    {
        $whereClause = '';
        $query = Entity::orderByFilter($filter);
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
        $whereClause = Entity::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $total = 0;
        $whereClause = Entity::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }
}
