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

    // The unit (អង្គភាព) the department belongs to
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    // A user tied to a unit (users.entity_id) only gets that unit's departments
    public static function getSingleOpt()
    {
        $entityId = Auth::user()?->entity_id;

        return $entityId ? self::getOptionByEntity($entityId) : self::options(Department::whereNotDeleted());
    }

    // The departments of one unit (department.entity_id)
    public static function getOptionByEntity($entityId)
    {
        return self::options(Department::whereNotDeleted()->where('entity_id', $entityId));
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
        $query = Department::with('entity')->orderByFilter($filter);
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
