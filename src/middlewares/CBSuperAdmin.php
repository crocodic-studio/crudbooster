<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use CRUDBooster;
use DB;

class CBSuperAdmin
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
        if(!cb()->isSuperadmin()) {
            return redirect(config('crudbooster.ADMIN_PATH').'/login')->with('message', trans('crudbooster.not_logged_in'));
        }
        
        return $next($request);
    }
}
