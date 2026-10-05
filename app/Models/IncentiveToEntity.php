<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class IncentiveToEntity extends Model
{
    protected $table = 'incentive_to_entity';
    // protected  $primaryKey = 'id';
    protected $fillable = [
        "incentive_id",
        "entity_id"

    ];
    public $timestamps = false;
}