<?php

namespace App\Exports;
use App\Models\Settings\AccountType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class AccountTypeExport implements FromCollection, WithHeadings
{
  public function collection(): \Illuminate\Support\Enumerable
  {
    $filter = array(
      "sort" => "id",
      "order" => "asc",
      "page_number" => 1,
      "limit" => 100,
      "search_field" => array()
    );
    $whereClause = "";
    $query = AccountType::orderByFilter($filter);
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
    
    $whereClause->whereNotDeleted();
    $results = collect($whereClause->get());
    return $results;
  }

    public function headings(): array
    {
        return [
            'id',
            'account_type_group_id',
            'name_en',
            'name_kh',
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
