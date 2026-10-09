<?php

namespace App\Imports;

use App\Models\OfficerPayable;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use OwenIt\Auditing\Contracts\Auditable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OfficerPayableImport implements Auditable, ToModel, WithHeadingRow
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @return Model|null
     */
    public function model(array $row): Model|array|null
    {
        $queryCheck = OfficerPayable::where('id', (int) ($row['id'] ?? null))->first();
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerPayable)->getTable());
        $str = '';
        $arr = [];
        for ($i = 0; $i < count($data['tables']); $i++) {
            // A column not in the file is left alone: an update keeps its value, a new row gets the table's default
            if (! array_key_exists($data['tables'][$i]->Field, $row)) {
                continue;
            }
            $field = $data['tables'][$i]->Field;
            $value = $row[$field] ?? null;
            if (isset($data['tables'][$i]->Field)) {
                try {
                    $arr[] = [$data['tables'][$i]->Field => $row[$data['tables'][$i]->Field]];
                    // code...
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            if (in_array($data['tables'][$i]->Field, ['issue_date', 'request_date', 'created_at'])) {
                if (is_numeric($value)) {
                    // ONLY call this if it's a numeric Excel serial (e.g. 20150)
                    $date = Date::excelToDateTimeObject($value);
                } else {
                    // If it's already a string like '1955-03-02', parse it with Carbon
                    try {
                        $date = Carbon::parse($value);
                    } catch (\Exception $e) {
                        $date = null; // Handle invalid date strings
                    }
                }

                $arr[] = [$data['tables'][$i]->Field => $date ? $date->format('Y-m-d') : null];
            }
        }

        if (! $queryCheck) {
            $pushArray = array_merge([['created_by' => Auth::user()->id]]);
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            OfficerPayable::insert($dataFields);
        } else {
            $pushArray = array_merge([['modified_by' => Auth::user()->id]]);
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            OfficerPayable::find((int) ($row['id'] ?? null))->update($dataFields);
        }

        return null; // the row is saved above, nothing for Laravel Excel to save
    }
}
