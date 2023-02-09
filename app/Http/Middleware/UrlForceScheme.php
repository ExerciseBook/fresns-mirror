<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;

class UrlForceScheme
{
    public function handle(Request $request, Closure $next)
    {
        if (\request()->secure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        return $next($request);
    }
}
