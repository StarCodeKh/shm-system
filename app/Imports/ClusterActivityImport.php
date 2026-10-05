<?php

namespace App\Imports;

use App\Models\Modules\ProgramManagement\ClusterActivity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
class ClusterActivityImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = ClusterActivity::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            if($row["sub_program_id"]!=""){
                ClusterActivity::insert([
                    "id" => $row["id"],
                    "sub_program_id" => $row["sub_program_id"],
                    "code" => $row["code"],
                    "sub_code" => $row["sub_code"],
                    "entity_id" => $row["entity_id"],
                    "entity_member_id" => $row["entity_member_id"],
                    "name_en" => $row["name_en"],
                    "name_kh" => $row["name_kh"],
                    "remark" => $row["remark"],
                    "order_level" => $row["order_level"],
                    "created_by" => Auth::user()->id
                ]);
            }
        }
    }
}
