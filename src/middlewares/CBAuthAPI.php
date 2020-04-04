<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use CRUDBooster;

class CBAuthAPI
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


        cb()->authAPI();

        return $next($request);
    }
}
