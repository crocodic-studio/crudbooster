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
        $adminPath = config('crudbooster.admin_path')?:'admin';

        if(CRUDBooster::myId()==''){
            $url = url($adminPath.'/login');
            return redirect($url)->with('message',trans('crudbooster.not_logged_in'));
        }

        if(CRUDBooster::isLocked()){
            $url = url($adminPath.'/lock-screen');
            return redirect($url);
        }

        return $next($request);
    }
}
