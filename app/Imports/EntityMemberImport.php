<?php

namespace App\Imports;

use App\Models\Settings\EntityMember;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class EntityMemberImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        $queryCheck = EntityMember::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            EntityMember::insert([
                "id" => $row["id"],
                "fullname"=>$row["fullname"],
                "entity_id"=>$row["entity_id"],
                "is_active"=> 1
            ]);
        }
    }
}
