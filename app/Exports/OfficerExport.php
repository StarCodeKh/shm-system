<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class OfficerExport implements FromCollection, WithHeadings,Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function collection(): \Illuminate\Support\Enumerable
    {
      // $filter = array(
      //   "sort" => "id",
      //   "order" => "asc",
      //   "page_number" => 1,
      //   "limit" => PHP_INT_MAX,
      //   "search_field" => array()
      // );
      // return Officer::getData($filter);
      
      $query = DB::table("officer")
                // ->offset(3 * 30000)
                // ->limit(30000)
                ->get();
      return $query;

      // return $query = DB::select('SELECT * FROM customer_payment');
      // $data = [];
      // foreach($query as $row){
      //   $data[] = array(
      //     "id" => $row->id,
      //     "payment_no" => $row->payment_no,
      //     "payment_id" => "",
      //     "customer_id" => "",
      //     "document" => "",
      //     "create_by" => "",
      //     "record_by" => "",
      //     "payment_date" => "",
      //     "payment_type" => "",
      //     "description" => "",
      //     "amount" => "",
      //     "entry_date" => "",
      //     "is_active" => "",
      //     "bu" => "",
      //     "ou" => ""
      //   );
      // } 
      // return $data;
    }

    public function headings(): array
    {
      // $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
      $data["tables"] = DB::select('show columns from tbl_officer');
      $str = "";
      for($i=0; $i < count($data["tables"]); $i++){
        $str .= $data["tables"][$i]->Field.",";
      }
      $newArr = explode(",", $str);
      array_pop($newArr);
      $dataFields = $newArr;
      return $dataFields;

        //  return [
        //     "id",
        //     "payment_no",
        //     "payment_id",
        //     "customer_id",
        //     "document",
        //     "create_by",
        //     "record_by",
        //     "payment_date",
        //     "payment_type",
        //     "description",
        //     "amount",
        //     "entry_date",
        //     "is_active",
        //     "bu",
        //     "ou"
        // ];
    }
}