<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use DB;


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
          // Defined once per process (a second define() is an error in PHP 9)
          if (!defined($value->key)) define($value->key, $value->value);
       }
    
       return $next($request);  
    }
}


