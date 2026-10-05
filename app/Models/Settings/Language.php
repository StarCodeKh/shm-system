<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
  protected $auditStrict = true;
  protected  $primaryKey = 'id';
  
  protected $table = 'mef_language';
  protected $fillable = [
                          "name",
                          "code",
                          "image",
                          "order_level",
                          "status"
                        ];
  public $timestamps = true;
}
