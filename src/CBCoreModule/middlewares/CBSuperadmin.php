<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\middlewares;

use Closure;
use Crocodicstudio\Crudbooster\helpers\CRUDBooster;

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
        $adminPath = cbConfig('ADMIN_PATH', 'admin');

        if (auth('cbAdmin')->guest()) {
            return redirect(url($adminPath.'/login'))->with('message', cbTrans('not_logged_in'));
        }

        if(!CRUDBooster::isSuperadmin()) {
            event('cb.unauthorizedTryToSuperAdminArea', [cbUser(), request()->fullUrl()]);
            return redirect($adminPath)->with(['message'=> cbTrans('denied_access'),'message_type'=>'warning']);
        }

        return $next($request);
    }
}
