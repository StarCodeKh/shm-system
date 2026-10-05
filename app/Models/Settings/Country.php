<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  protected $auditStrict = true;
  protected  $primaryKey = 'id';
  
  protected $table = 'mef_country';
  protected $fillable = [
                          "name_en",
                          "name_kh",
                          "order_level",
                          "remark",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
  public $timestamps = true;
}
