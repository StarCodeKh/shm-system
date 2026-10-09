<?php

namespace App\Models\Settings;

use CommonService;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    protected $table = 'mef_planning';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name_en',
        'name_kh',
        'year',
        'start_date',
        'end_date',
        'is_default',
        'created_by',
        'modified_by',
    ];

    public $timestamps = true;

    public static function getPlannings($filter)
    {
        $data = [];
        $whereClause = CommonService::queryTable(Planning::class, $filter, $flag = 1); // Flag == 1 mean if query result limit by qty
        $results = collect($whereClause->get());
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'name_en' => $row->name_en,
                'name_kh' => $row->name_kh,
                'year' => $row->year,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'is_default' => $row->is_default,
            ];
        }

        return $data;
    }

    public static function getCount($filter)
    {
        $whereClause = CommonService::queryTable(Planning::class, $filter, $flag = 1); // Flag == 1 mean if query result limit by qty
        $total = $whereClause->count();

        return $total;
    }
}
