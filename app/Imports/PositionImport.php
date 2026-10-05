<?php

namespace App\Imports;

use App\Models\Settings\Position;
use App\Models\Settings\PositionTypeGroup;
use App\Models\Settings\PositionType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class PositionImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = Position::where("name_kh", "Like", "%".$row["name_kh"]."%")->first();
        if(!$queryCheck){
            Position::insert([
                "id" => $row["id"],
                "name_en" => $row["name_en"],
                "name_kh" => $row["name_kh"],
                "created_by" => Auth::user()->id
            ]);
        }

        // $queryCheck2 = PositionTypeGroup::where("name_kh", "Like", "%".$row["name_kh"]."%")->first();
        // if(!$queryCheck2 && $row["position_type_group_id"]==""){
        //     PositionTypeGroup::insert([
        //         "id" => $row["id"],
        //         "name_en" => $row["name_en"],
        //         "name_kh" => $row["name_kh"]
        //     ]);
        // }

        // $queryCheck3 = PositionType::where("name_kh", "Like", "%".$row["name_kh"]."%")->first();
        // if(!$queryCheck3 && $row["position_type_group_id"]!=""){
        //     PositionType::insert([
        //         "id" => $row["id"],
        //         "parent_id" => $row["parent_id"],
        //         "name_en" => $row["name_en"],
        //         "name_kh" => $row["name_kh"]
        //     ]);
        // }
    }
}