<?php

namespace App\Imports;

use App\Models\Settings\Department;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use OwenIt\Auditing\Contracts\Auditable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DepartmentImport implements Auditable, ToModel, WithHeadingRow
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @return Model|null
     */
    public function model(array $row): Model|array|null
    {
        $queryCheck = Department::where('id', (int) ($row['id'] ?? null))->first();
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new Department)->getTable());
        $str = '';
        $arr = [];
        for ($i = 0; $i < count($data['tables']); $i++) {
            // A column not in the file is left alone: an update keeps its value, a new row gets the table's default
            if (! array_key_exists($data['tables'][$i]->Field, $row)) {
                continue;
            }
            if (isset($data['tables'][$i]->Field)) {
                $arr[] = [$data['tables'][$i]->Field => $row[$data['tables'][$i]->Field]];
            }
            if ($data['tables'][$i]->Field == 'user_id') {
                $arr[] = [$data['tables'][$i]->Field => null];
            }
            if ($data['tables'][$i]->Field == 'register_date') {
                $arr[] = [$data['tables'][$i]->Field => Carbon::instance(Date::excelToDateTimeObject($row[$data['tables'][$i]->Field]))];
            }
            if ($data['tables'][$i]->Field == 'dob') {
                $arr[] = [$data['tables'][$i]->Field => Carbon::instance(Date::excelToDateTimeObject($row[$data['tables'][$i]->Field]))];
            }
        }
        if (! $queryCheck) {
            $pushArray = array_merge([['created_by' => Auth::user()->id]]);
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Department::insert($dataFields);
        } else {
            $pushArray = array_merge([['modified_by' => Auth::user()->id]]);
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Department::find((int) ($row['id'] ?? null))->update($dataFields);
        }

        return null; // the row is saved above, nothing for Laravel Excel to save
    }
}
