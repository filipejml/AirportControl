<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheRelatorioResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'relatorios:response:' . sha1($request->fullUrl());

        if ($cached = Cache::get($key)) {
            return response($cached['content'], $cached['status'], $cached['headers'])
                ->header('X-Report-Cache', 'HIT');
        }

        $response = $next($request);

        if ($response->isSuccessful() && str_contains((string) $response->headers->get('Content-Type'), 'application/json')) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => ['Content-Type' => 'application/json'],
            ], now()->addMinute());
            $response->headers->set('X-Report-Cache', 'MISS');
        }

        return $response;
    }
}
