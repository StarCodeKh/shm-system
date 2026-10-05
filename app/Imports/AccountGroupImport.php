<?php

namespace App\Imports;

use App\Models\Settings\AccountGroup;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class AccountGroupImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        $queryCheck = AccountGroup::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            AccountGroup::insert([
                "code"=>$row["code"],
                "name_en"=>$row["name_en"],
                "name_kh"=>$row["name_kh"],
                "order_level"=>$row["order_level"],
            ]);
        }
    }
}
