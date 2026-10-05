<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class PositionType extends Model
{
    protected $table = 'position_type';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'parent_id',
                            "name_en",
                            "name_kh",
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
