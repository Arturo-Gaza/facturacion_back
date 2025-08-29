<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseTime
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $ms = round((microtime(true) - $start) * 1000, 2);
        $response->headers->set('X-Response-Time', "{$ms} ms");

        return $response;
    }
}
