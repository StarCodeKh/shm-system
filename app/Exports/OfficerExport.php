<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Officer;
use DB;
use Spatie\Permission\Traits\HasRoles;
use CommonService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;

use App\Models\CustomerPayment;

class OfficerExport implements FromCollection, WithHeadings,Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function collection(): \Illuminate\Support\Enumerable
    {
      // $filter = array(
      //   "sort" => "id",
      //   "order" => "asc",
      //   "page_number" => 1,
      //   "limit" => 1000000000000000000000,
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
      // $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new Officer)->getTable());
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