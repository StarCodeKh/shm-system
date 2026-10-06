<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
class DepartmentImport implements ToModel,WithHeadingRow,Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Department::where("id", (int)$row["id"])->first();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Department)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            if(isset($data["tables"][$i]->Field)){
                $arr[] = [$data["tables"][$i]->Field => $row[$data["tables"][$i]->Field]];
            }
            if($data["tables"][$i]->Field == 'user_id'){
                $arr[] = [$data["tables"][$i]->Field => null];
            }
            if($data["tables"][$i]->Field == 'register_date'){
                $arr[] = [$data["tables"][$i]->Field => Carbon::instance(Date::excelToDateTimeObject($row[$data["tables"][$i]->Field]))];
            }
            if($data["tables"][$i]->Field == 'dob'){
                $arr[] = [$data["tables"][$i]->Field => Carbon::instance(Date::excelToDateTimeObject($row[$data["tables"][$i]->Field]))];
            }
        }
        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Department::insert($dataFields);
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Department::find((int)$row["id"])->update($dataFields);
        }
    }
}
