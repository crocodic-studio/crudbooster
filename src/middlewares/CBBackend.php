<?php

namespace crocodicstudio\crudbooster\middlewares;

use App\Http\CBHook;
use Closure;
use CRUDBooster;

class CBBackend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->guest()) {
            return cb()->redirect(cb()->getLoginUrl("login"),trans('crudbooster.not_logged_in'),'warning');
        }

        CBHook::beforeBackendMiddleware($request);

        $response = $next($request);

        CBHook::afterBackendMiddleware($request, $response);

        return $response;

    }
}
