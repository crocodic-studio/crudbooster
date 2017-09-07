<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use CRUDBooster;
use CB;

class CBSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $admin_path = cbConfig('ADMIN_PATH', 'admin');

        if(CRUDBooster::myId()==''){
            $url = url($admin_path.'/login'); 
            return redirect($url)->with('message',trans('crudbooster.not_logged_in'));
        }

        if(!CRUDBooster::isSuperadmin()) {
            return redirect($admin_path)->with(['message'=>trans('crudbooster.denied_access'),'message_type'=>'warning']);
        }

        if(CRUDBooster::isLocked()){
            $url = url($admin_path.'/lock-screen');
            return redirect($url);
        }

        return $next($request);
    }
}
