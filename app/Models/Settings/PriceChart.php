<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class PriceChart extends Model
{
    protected $table = 'price_chart';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            "name_en",
                            "name_kh",
                            'order_level',
                            'status',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
