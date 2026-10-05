<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
class CustomerPayment extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable,HasRoles;
  
  protected $table = 'customer_payment';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;


}