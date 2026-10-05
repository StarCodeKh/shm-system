<?php

namespace App\Imports;

use App\Models\Settings\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class SubAccountImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        $queryCheck = Account::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            Account::insert([
                "parent_id"=>$row["parent_id"],
                "code"=>$row["code"],
                "name_en"=>$row["name_en"],
                "name_kh"=>$row["name_kh"],
                "description"=>$row["description"],
                "order_level"=>$row["order_level"],
            ]);
        }
    }
}
