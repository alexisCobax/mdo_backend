<?php

namespace App\Http\Middleware;

use Closure;

class SetUtf8Charset
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // $response->header('Content-Type', 'text/html; charset=utf-8');

        return $response;
    }
}