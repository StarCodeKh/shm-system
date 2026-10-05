<?php

namespace App\Imports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
class OutcomeImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // // dd($row);
        // $year = 2020;
        $entity_id = 0;
        $entityInfo = DB::table("entity as e")->Select("e.name as entity","e.id")->where("code", $row["code"])->first();
        // // dd($entityInfo);
        if($entityInfo){
            $entity_id = $entityInfo->id;
        //     $issue_date = "";
        //     switch ($i){
        //         case "1":
        //             # code...
        //             $issue_date = $year."-".$month."-01";
        //             break;
                
        //         case '2':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '3':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '4':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '5':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '6':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '7':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '8':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '9':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '10':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '11':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         case '12':
        //             $issue_date = $year."-".$month."-01";
        //             # code...
        //             break;
        //         default:
        //             $issue_date = $year."-01-01";
        //             break;
            // }
        }
        // Expense::where("issue_date", $issue_date)->delete();
        Expense::insert([
            // "account_group" => $row["account_group"],
            // "account_type" => $row["account_type"],
            // "account" => $row["account"],
            "title" => "របាយការណ៍ចំណាយ របស់ ".$row["entity"]."",
            // "description" => $row["description"],
            // "issue_date" => $row["issue_date"],
            // "category" => $row["category"],
            // "payment_method" => $row["payment_method"],
            "amount" => round($row["total_incentive_with_tax"]),
            "exchange_rate" => '4000',//$row["exchange_rate"],
            // "reference" => $row["reference"],
            "entity" => $row["entity"],
            "entity_id" => $entity_id,
            "currency" => "រៀល",
        ]);
    }
}
