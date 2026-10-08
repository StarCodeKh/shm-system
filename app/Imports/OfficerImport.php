<?php

namespace App\Imports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Currency;
use App\Models\Officer;
use App\Models\OfficerPaymentRecurring;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\Models\Settings\Status;
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
        $queryCheck = Officer::where("id", (int)($row["id"] ?? null))->first();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
        $str = "";
        $arr = array();
        for($i=0; $i < count($data["tables"]); $i++){
            // A column not in the file is left alone: an update keeps its value, a new row gets the table's default
            if (!array_key_exists($data["tables"][$i]->Field, $row)) continue;
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

        // The officer first: the payment schedule below needs its id (a new officer's id is known only once saved)
        if(!$queryCheck){
            $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            $officerId = !empty($dataFields["id"]) ? (int) $dataFields["id"] : null;
            if ($officerId) {
                Officer::insert($dataFields);
            } else {
                unset($dataFields["id"]);
                $officerId = Officer::insertGetId($dataFields);
            }
        }else{
            $pushArray = array_merge(array(["modified_by" => Auth::user()->id]));
            $arraySingle = array_merge($arr, $pushArray);
            $result = call_user_func_array('array_merge', $arraySingle);
            $dataFields = $result;
            $officerId = $queryCheck->id;
            $queryCheck->update($dataFields);
        }

        // Every officer has a yearly payment schedule, starting on the register date
        $checkOfficerPaymentRecurring = OfficerPaymentRecurring::where("officer_id", $officerId)->first();
        if(!$checkOfficerPaymentRecurring){
            $registerDate = $dataFields["register_date"] ?? null;
            $checkExist = OfficerPaymentRecurring::create(
                [
                    "officer_id" => $officerId,
                    "payment_term" => "Yearly",
                    "recurring_amount" => 60000,
                    "currency_id" => Currency::idOf(Currency::KHR),
                    "executed_num" => 1,
                    "schedule_start_date" => $registerDate ? Carbon::parse($registerDate) : Carbon::today(),
                    "schedule_end_date" => "2100-01-01",
                    "remark" => "",
                    "status" => Status::ACTIVE,
                    "is_default" => 1,
                ]
            );
        }
        return null; // the row is saved above, nothing for Laravel Excel to save
    }
}
