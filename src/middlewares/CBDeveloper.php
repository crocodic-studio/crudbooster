<?php

namespace crocodicstudio\crudbooster\middlewares;

use App\Http\Controllers\CBHook;
use Closure;
use CRUDBooster;

class CBDeveloper
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

        if(!session()->has("developer")) {
            return cb()->redirect(cb()->getDeveloperUrl("login"),"Please login for first");
        }

        return $next($request);

    }
}
