<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExpenseExport implements FromCollection, WithHeadings,Auditable
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
    $whereClause = "";
    $query = Expense::orderBy($filter["sort"], $filter["order"]);
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
            'expense_title',
            'account',
            'payment_method',
            'issue_date',
            'category',
            'amount',
            'reference',
            'description',
            'entity_id',
            'exchange_rate',
            'currency',
            'status',
            'created_by',
            'modified_by',
            'created_at',
            'updated_at'
        ];
    }
}