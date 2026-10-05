<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use DB;
use App\user;
use Validator;
use Auth;
use Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;


class GlobalSite
{

    protected $auth;
    /**
    * Create a new filter instance.
    *
    * @param  Guard  $auth
    * @return void
    */

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
    //    $config_site = DB::table('config')->where('config_group_id', 1)->get(); 
        $config_site = DB::table('config')->get(); 
       foreach ($config_site as $key => $value) {
          define($value->key, $value->value);
       }
    
       return $next($request);  
    }
}


