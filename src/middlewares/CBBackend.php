<?php

namespace crocodicstudio\crudbooster\middlewares;

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
     */
    public function handle($request, Closure $next)
    {   
        $adminPath = cbConfig('ADMIN_PATH', 'admin');

        if(CRUDBooster::myId()==''){
            $url = url($adminPath.'/login'); 
            return redirect($url)->with('message',cbTrans('not_logged_in'));
        }

        if(CRUDBooster::isLocked()){
            $url = url($adminPath.'/lock-screen');
            return redirect($url);
        }

        $moduleName = $request->segment(2);
        $module = CRUDBooster::getCurrentModule();
        $exception = ['notifications','users/profile','users/edit-save'];

        if(count($exception)) {
            foreach($exception as $e) {                
                if($request->is($adminPath.'/'.$e.'*')) {
                    return $next($request);
                }
            }
        }

        if($request->is($adminPath)) {
            return $next($request);
        }

        $url = $adminPath . '/' . $moduleName;
        if($request->is($url .'*') && !CRUDBooster::canView()) {
                CRUDBooster::insertLog(cbTrans('log_try_view',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($url.'/add*') && !CRUDBooster::canCreate()) {
                CRUDBooster::insertLog(cbTrans('log_try_add',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($url.'/edit*') && !CRUDBooster::canUpdate()) {
                CRUDBooster::insertLog(cbTrans('log_try_edit',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($url.'/delete*') && !CRUDBooster::canDelete()) {
                CRUDBooster::insertLog(cbTrans('log_try_delete',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($url.'/detail*') && !CRUDBooster::canRead()) {
                CRUDBooster::insertLog(cbTrans('log_try_view',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        return $next($request);
    }
}
