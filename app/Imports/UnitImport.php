<?php

namespace App\Imports;

use App\Models\Settings\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class UnitImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Unit::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            Unit::insert([
                "id"=> $row["id"],
                "name_en"=>$row["title_en"],
                "name_kh"=>$row["title_kh"],
                "order_level"=>$row["order_level"]
            ]);
        }
    }
}
