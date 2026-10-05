<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_method';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'name',
                            'description'
                          ];
    public $timestamps = true;
}
