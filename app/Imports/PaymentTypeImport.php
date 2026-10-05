<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\PaymentType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class PaymentTypeImport implements ToModel,WithHeadingRow,Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = PaymentType::where("id", (int)$row["id"])->first();
        $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new PaymentType)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            $arr[] = [$data["tables"][$i]->Field => $row[$data["tables"][$i]->Field]];
        }
        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            PaymentType::insert($dataFields);
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            PaymentType::find((int)$row["id"])->update($dataFields);
        }
    }
}