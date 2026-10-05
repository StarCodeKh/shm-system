<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // $trusted_domains = ["http://127.0.0.1:9004", "http://localhost:9003"];
        // // header("Access-Control-Allow-Origin: *");
        // // header("Access-Control-Allow-Headers: ACCEPT, CONTENT-TYPE, X-CSRF-TOKEN");
        // // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
        // $headers = [
        //     'Access-Control-Allow-Origin' => '*',
        //     'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        //     'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        //     'Access-Control-Max-Age' => '86400',
        //     // 'Content-Type'> 'application/xlsx',
        //     // 'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
        //     // 'Access-Control-Allow-Credentials' => true,
        //     // 'Access-Control-Allow-Headers'=> 'Access-Control-Allow-Origin, Origin, X-XSRF-TOKEN, Content-Type, X-Auth-Token, Authorization, X-Requested-With'
        // ];
        // if($request->getMethod() == "OPTIONS") {

        //     return response()->make('OK', 200, $headers);
        // }

        // $response = $next($request);
        // foreach($headers as $key => $value)
        // $response->header($key, $value);
        // return $response;

        $response = $next($request);

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Max-Age' => '86400',
        ];

        // Handle regular Laravel Response and Symfony Response
        foreach ($headers as $name => $value) {
            if ($response instanceof BinaryFileResponse) {
                // BinaryFileResponse uses headers->set()
                $response->headers->set($name, $value);
            } elseif (method_exists($response, 'header')) {
                // Laravel Response has header() method
                $response->header($name, $value);
            } else {
                // Fallback: manually set Symfony component header
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }
}
