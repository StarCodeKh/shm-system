<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\OfficerIncentive;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
class OfficerIncentiveImport implements ToModel,WithHeadingRow,Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = OfficerIncentive::where("id", (int)$row["id"])->first();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerIncentive)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            $field = $data["tables"][$i]->Field;
            $value = $row[$field] ?? null;
            if(isset($data["tables"][$i]->Field)){
                // A column missing from the sheet is imported as empty instead of stopping the import
                $arr[] = [$field => $value];
            }

            if(in_array($data["tables"][$i]->Field,["issue_date", "request_date", "created_at"])){
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

                $arr[] = [$data["tables"][$i]->Field => $date ? $date->format('Y-m-d') : null];
            }
        }

        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            OfficerIncentive::insert($dataFields);
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            OfficerIncentive::find((int)$row["id"])->update($dataFields);
        }
    }
}
