<?php

namespace App\Fresns\Panel\Http\Middleware;

use Closure;

class ChangeLocale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->get('lang', 'zh-Hans');

        \App::setLocale($locale);

        return $next($request);
    }
}
