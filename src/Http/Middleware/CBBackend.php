<?php

namespace Crocodicstudio\Crudbooster\Http\Middleware;

use Closure;
use CRUDBooster;

class CBBackend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
	 * #Todo: Create CB own auth.
     */
    public function handle($request, Closure $next)
    {
        $admin_path = config('crudbooster.ADMIN_PATH')?:'admin';

        if(CRUDBooster::myId()==''){
            $url = url($admin_path.'/login');
            return redirect($url)->with('message',trans('crudbooster.not_logged_in'));
        }

        if(CRUDBooster::isLocked()){
            $url = url($admin_path.'/lock-screen');
            return redirect($url);
        }

        return $next($request);
    }
}
