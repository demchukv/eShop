<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class LogoutMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $headers = [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
