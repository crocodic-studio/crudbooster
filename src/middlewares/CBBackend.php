<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;

class CBBackend
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
        $admin_path = config('crudbooster.ADMIN_PATH')?:'admin';

        if(get_my_id()==''){
            $url = url($admin_path.'/login'); 
            return redirect($url)->with('message','You are not logged in !');
        }
        if(get_is_locked()){
            $url = url($admin_path.'/lock-screen');
            return redirect($url);
        }

        return $next($request);
    }
}
