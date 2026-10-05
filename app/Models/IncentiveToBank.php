<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class IncentiveToBank extends Model
{
    protected $table = 'incentive_to_bank';
    protected  $primaryKey = 'id';
    protected $fillable = [
        "incentive_id",
        "bank_id",
        "remark",
        "total_incentive",
        "exchange_rate"
    ];
    public $timestamps = false;
}