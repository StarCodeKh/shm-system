<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    protected $table = 'duty';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            "name_en",
                            "name_kh",
                            'incentive',
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
