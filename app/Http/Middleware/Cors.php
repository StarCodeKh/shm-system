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
