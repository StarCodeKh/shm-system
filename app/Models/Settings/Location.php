<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location';
    protected $fillable = [
                            "name_en",
                            "name_kh",
                            'remark',
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
