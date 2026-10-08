<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class AccountImport implements ToModel,WithHeadingRow,Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Account::where("id", (int)($row["id"] ?? null))->first();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Account)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            // A column not in the file is left alone: an update keeps its value, a new row gets the table's default
            if (!array_key_exists($data["tables"][$i]->Field, $row)) continue;
            $arr[] = [$data["tables"][$i]->Field => $row[$data["tables"][$i]->Field]];
        }
        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Account::insert($dataFields);
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Account::find((int)($row["id"] ?? null))->update($dataFields);
        }
        return null; // the row is saved above, nothing for Laravel Excel to save
    }
}
