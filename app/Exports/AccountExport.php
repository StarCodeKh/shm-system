<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Account;
use DB;
use Spatie\Permission\Traits\HasRoles;
use CommonService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ToModel;
class AccountExport implements FromCollection, WithHeadings,Auditable
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
    $whereClause = "";
    $query = Account::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }
    
    $whereClause->whereNotIn("status", [4]);
    $whereClause->orWhereNull("status");
    $results = collect($whereClause->get());
    return $results;
  }

    public function headings(): array
    {
        return [
            'id',
            'code',
            'parent_code',
            'name_en',
            'name_kh',
            'account_type',
            'description',
            'order_level',
            'status',
            'created_by',
            'modified_by',
            'created_at',
            'updated_at'
        ];
    }
}