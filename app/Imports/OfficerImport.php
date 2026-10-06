<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Officer;
use App\Models\OfficerPaymentRecurring;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
class OfficerImport implements ToModel,WithHeadingRow,Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Officer::where("id", (int)$row["id"])->first();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            $field = $data["tables"][$i]->Field;
            $value = $row[$field] ?? null;
            if(isset($data["tables"][$i]->Field)){
                $arr[] = [$data["tables"][$i]->Field => $row[$data["tables"][$i]->Field]];
            }
            if($data["tables"][$i]->Field == 'user_id'){
                $arr[] = [$data["tables"][$i]->Field => null];
            }

            if(in_array($data["tables"][$i]->Field,["register_date", "dob", "created_at", "updated_at"])){
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

        $checkOfficerPaymentRecurring = OfficerPaymentRecurring::where("officer_id", $row["id"])->first();
        if(!$checkOfficerPaymentRecurring){
            $checkExist = OfficerPaymentRecurring::create(
                [
                    "officer_id" => $row["id"],
                    "payment_term" => "Yearly",
                    "recurring_amount" => 60000,
                    "currency_id" => 1,
                    "executed_num" => 1,
                    "schedule_start_date" => Carbon::instance(Date::excelToDateTimeObject($row["register_date"])),
                    "schedule_end_date" => "2100-01-01",
                    "remark" => "",
                    "status" => 13,
                    "is_default" => 1,
                ]
            );
        }

        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Officer::insert($dataFields);
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            Officer::find((int)$row["id"])->update($dataFields);
        }
    }
}
