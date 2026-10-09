<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Standard browser security headers on every response (pages and API).
 * Files served straight from public/ get the same headers from public/.htaccess.
 *
 * The Content-Security-Policy covers framing, plugins, <base> and form targets. A script-src policy
 * would also need nonces for the inline <script> blocks in resources/views/application.blade.php.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',
            'Content-Security-Policy' => "frame-ancestors 'self'; object-src 'none'; base-uri 'self'; form-action 'self'",
        ];
        // HSTS only means something over HTTPS (browsers ignore it on plain HTTP)
        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
