<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Circular extends Model
{
    protected $table = 'circular';
    protected $fillable = [
        'achivement',
        'incentive',
        'shape',
        'color',
        'order_level',
        'remark',
        'created_by',
        'modified_by'
    ];
    public $timestamps = false;
}
