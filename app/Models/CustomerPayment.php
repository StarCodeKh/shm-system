<?php

namespace App\Models;
use App\Models\CompositionRequest;
use App\Models\Officer;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\Status;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\User;
use DB;
use Auth;
use CommonService;
class CustomerPayment extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable,HasRoles;
  
  protected $table = 'customer_payment';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;


}