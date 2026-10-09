<?php

namespace App\Models;

use App\Models\Concerns\HasDataScope;
use CommonService;
use DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FileManager extends Model implements Auditable
{
    use HasDataScope, \OwenIt\Auditing\Auditable;

    protected $table = 'file_manager';

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $guarded = [];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:'.('Y-m-d'),
        'updated_at' => 'datetime:'.('Y-m-d'),
    ];

    public static function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new FileManager)->getTable());
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
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new FileManager)->getTable());
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
        $query = FileManager::orderByFilter($filter)->whereNotDeleted()->get();
        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'label' => CommonService::localName($row),
                'value' => $row->id,
            ];
        }

        return $data;
    }

    // Filter fields of the file picker (Search panel): name and type
    public static function searchAttributes()
    {
        return [
            ['name' => 'file_name', 'type' => 'text'],
            ['name' => 'type', 'type' => 'select'],
        ];
    }

    // Type choices: the types of the stored files, with how many there are, e.g. "PDF (3)"
    public static function typeOptions()
    {
        return FileManager::query()->whereNotDeleted()->whereNotNull('type')
            ->select('type', DB::raw('count(*) as total'))->groupBy('type')->orderBy('type')->get()
            ->map(fn ($row) => ['label' => strtoupper($row->type).' ('.$row->total.')', 'value' => $row->type])
            ->values()->all();
    }

    public static function checkQuery($filter)
    {
        $query = FileManager::orderByFilter($filter);
        $whereClause = $query;

        $whereClause->whereNotDeleted();
        if ($filter['search_field']) {
            $arraySingle = call_user_func_array('array_merge', $filter['search_field']);
            $dataFields = $arraySingle;
            if (isset($filter['search_field'])) {
                for ($i = 0; $i < count($filter['search_field']); $i++) {
                    $field = array_key_first($filter['search_field'][$i]);
                    if (array_key_exists($field, $dataFields)) {
                        $value = $filter['search_field'][$i][$field]['value'] ?? $filter['search_field'][$i][$field];
                        if ($field === 'type') {
                            $whereClause->where('type', $value); // exact: "doc" must not match "docx"
                        } else {
                            $whereClause->Where($field, 'Like', '%'.$value.'%');
                        }
                    }
                }
            }
        }

        return $whereClause;
    }

    public static function getData($filter)
    {
        $whereClause = FileManager::checkQuery($filter);
        $whereClause->offset(($filter['page_number'] - 1) * $filter['limit']);
        $whereClause->limit($filter['limit']);
        $data = collect($whereClause->get());

        return $data;
    }

    public static function getTotal($filter)
    {
        $whereClause = FileManager::checkQuery($filter);
        $total = $whereClause->count();

        return $total;
    }
}
