<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\PaymentType;
use DB;
use Spatie\Permission\Traits\HasRoles;
use CommonService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;
class PaymentTypeExport implements FromCollection, WithHeadings,Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function collection(): \Illuminate\Support\Enumerable
    {
      $filter = array(
        "sort" => "id",
        "order" => "asc",
        "page_number" => 1,
        "limit" => 1000000000000000000000,
        "search_field" => array()
      );
      return PaymentType::getData($filter);
    }

    public function headings(): array
    {
      $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new PaymentType)->getTable());
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