<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\PaymentType;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class PaymentTypeExport implements FromCollection, WithHeadings,Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function collection(): \Illuminate\Support\Enumerable
    {
      $filter = array(
        "sort" => "id",
        "order" => "asc",
        "page_number" => 1,
        "limit" => PHP_INT_MAX,
        "search_field" => array()
      );
      return PaymentType::getData($filter);
    }

    public function headings(): array
    {
      $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new PaymentType)->getTable());
      $str = "";
      for($i=0; $i < count($data["tables"]); $i++){
        $str .= $data["tables"][$i]->Field.",";
      }
      $newArr = explode(",", $str);
      array_pop($newArr);
      $dataFields = $newArr;
      return $dataFields;
    }
}