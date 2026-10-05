<?php

namespace App\Imports;

use App\Models\Modules\ProgramManagement\SubProgram;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
class SubProgramImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = SubProgram::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            SubProgram::insert([
                "id"=> $row["id"],
                "program_id"=>$row["program_id"],
                "code"=>$row["code"],
                "sub_code"=>$row["sub_code"],
                "structure_name_en"=>$row["structure_name_en"],
                "structure_name_kh"=>$row["structure_name_kh"],
                "entity_id"=>$row["entity_id"],
                "entity_member_id"=>$row["entity_member_id"],
                "name_en"=>$row["name_en"],
                "name_en"=>$row["name_en"],
                "name_kh"=>$row["name_kh"],
                "remark"=>$row["remark"],
                "administrative_class" => $row["administrative_class"],
                "order_level"=>$row["order_level"],
                "created_by"=>Auth::user()->id
            ]);
        }
    }
}
