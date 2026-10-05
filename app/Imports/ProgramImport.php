<?php

namespace App\Imports;

use App\Models\Modules\ProgramManagement\Program;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class ProgramImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        $queryCheck = Program::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            Program::insert([
                "id" => $row["id"],
                "code"=>$row["code"],
                "sub_code"=>$row["sub_code"],
                "objective_id"=>$row["objective_id"],
                "entity_id"=>$row["entity_id"],
                "entity_member_id"=>$row["entity_member_id"],
                "name_en"=>$row["name_en"],
                "name_kh"=>$row["name_kh"],
                "remark"=>$row["remark"],
                "order_level"=>$row["order_level"],
                "created_by"=>Auth::user()->id
            ]);
        }
    }
}
