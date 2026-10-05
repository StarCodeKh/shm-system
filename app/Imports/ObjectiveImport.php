<?php

namespace App\Imports;

use App\Models\Modules\ProgramManagement\Objective;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
class ObjectiveImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Objective::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            Objective::insert([
                "id" => $row["id"],
                "name_en" => $row["name_en"],
                "name_kh" => $row["name_kh"],
                "remark" => $row["remark"],
                "order_level" => $row["order_level"],
            ]);
        }
    }
}
