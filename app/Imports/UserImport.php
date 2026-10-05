<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class UserImport implements ToModel,WithHeadingRow
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        if(isset($row["email"]) && $row["email"]!=""){
            // $queryCheck = User::where("email", $row["email"])->first();
            // if(!$queryCheck){
            //     User::insert([
            //         "role_id" => 1,
            //         "entity_id" => $row["entity_id"],
            //         "name" => $row["fullname"],
            //         "username" => $row["email"],
            //         "email" => $row["email"],
            //         "title" => "",
            //         "latin" => $row["latin"],
            //         "department" => $row["entity"],
            //         "duty" => "",
            //         "entity" => $row["entity"],
            //         "position" => $row["position"],
            //         "avatar" => "",
            //         "gender" => $row["gender"],
            //         "dob" => "",
            //         "telephone_1" => $row["telephone_1"],
            //         "telephone_2" => $row["telephone_2"],
            //         "password" => bcrypt("123456"),
            //         "is_default" => 0,
            //         "is_active" => 1,
            //         "created_by"=>Auth::user()->id
            //     ]);
            // }else{
                User::where("email", $row["email"])->update([
                    "role_id" => 1,
                    "entity_id" => $row["entity_id"],
                    "name" => $row["fullname"],
                    "username" => $row["email"],
                    "email" => $row["email"],
                    "title" => "",
                    "latin" => $row["latin"],
                    "department" => $row["entity"],
                    "duty" => "",
                    "entity" => $row["entity"],
                    "position" => $row["position"],
                    "avatar" => "",
                    "gender" => $row["gender"],
                    "dob" => "",
                    "telephone_1" => $row["telephone_1"],
                    "telephone_2" => $row["telephone_2"],
                    "password" => bcrypt("123456"),
                    "is_default" => 0,
                    "is_active" => 1,
                    "created_by"=>Auth::user()->id
                ]);
            // }
        }
    }
}
