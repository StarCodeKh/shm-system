<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'device_token',
                            'telephone',
                            'otp_code',
                            'expired_date',
                            'remark',
                            'is_verify',
                            'created_at'
                          ];

    public $timestamps = false;

}
