<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class PositionTypeGroup extends Model
{
    protected $table = 'position_type_group';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            "name_en",
                            "name_kh",
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
