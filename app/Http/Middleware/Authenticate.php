<?php

namespace App\Http\Middleware;

use App\Http\Fresns\FresnsApi\Base\FresnsBaseApiController;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {

        try{
            $this->authenticate($request, $guards);
        }catch(\Exception $e){
            
            return redirect('/fresns/login');
        }
        return $next($request);
    }
}
