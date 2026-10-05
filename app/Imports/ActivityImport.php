<?php

namespace App\Imports;

use App\Models\Modules\ProgramManagement\Activity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
class ActivityImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Activity::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            if($row["cluster_activity_id"]!=""){
                Activity::insert([
                    "id" => $row["id"],
                    "cluster_activity_id" => $row["cluster_activity_id"],
                    "code" => $row["code"],
                    "sub_code" => $row["sub_code"],
                    "entity_id" => $row["entity_id"],
                    "entity_member_id" => $row["entity_member_id"],
                    "name_en" => $row["name_en"],
                    "name_kh" => $row["name_kh"],
                    "remark" => "",
                    "is_active" => 1,
                    "order_level" => $row["order_level"],
                    "created_by" => Auth::user()->id
                ]);
            }
        }
    }
}
