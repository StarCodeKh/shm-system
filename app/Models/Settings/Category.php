<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $table = 'mef_item_category';
  protected  $primaryKey = 'id';
  protected $fillable = [
                          "name_en",
                          "name_kh",
                          "order_level",
                          'remark',
                          "is_active",
                          "status",
                          "created_by",
                          "modified_by",
                        ];
  public $timestamps = false;

  public static function getCategories($params){
    $query = Category::orderBy("order_level")->whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => (config_language == "en")?$row->name_en:$row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }
}
