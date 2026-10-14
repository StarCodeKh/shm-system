<?php

namespace App\Models\Settings;

use Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Department extends Model implements Auditable
{
    use HasRoles,\OwenIt\Auditing\Auditable;

    protected $table = 'department';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = false;

    // A user tied to a unit (users.entity_id) only gets that unit's departments
    public static function getSingleOpt()
    {
        $entityId = Auth::user()?->entity_id;

        return $entityId ? self::getOptionByEntity($entityId) : self::options(Department::whereNotDeleted());
    }

    // Sections of one general department: a section's code starts with its entity's code (entity 01 -> 010001, 010002...)
    public static function getOptionByEntity($entityId)
    {
        $entityCode = Entity::where('id', $entityId)->value('code');
        if (! $entityCode) {
            return [];
        }

        return self::options(Department::whereNotDeleted()->where('code', 'like', $entityCode.'%'));
    }

    private static function options($query)
    {
        $data = [];
        foreach ($query->orderByLevel()->get() as $row) {
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
        $query = Department::orderByFilter($filter);
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
        $whereClause = Department::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $total = 0;
        $whereClause = Department::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }
}
