<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use CRUDBooster;
use DB;

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
        $admin_path = config('crudbooster.ADMIN_PATH') ?: 'admin';

        if (cb()->myId() == '') {
            $url = url($admin_path.'/login');
            return redirect($url)->with('message', trans('crudbooster.not_logged_in'));
        }

        if (cb()->isLocked()) {
            $url = url($admin_path.'/lock-screen');
            return redirect($url);
        }
        
        return $next($request);
    }
}
