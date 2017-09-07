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
            return redirect($url)->with('message',trans('crudbooster.not_logged_in'));
        }

        if(CRUDBooster::isLocked()){
            $url = url($adminPath.'/lock-screen');
            return redirect($url);
        }

        $moduleName = $request->segment(2);
        $moduleMethod = $request->segment(3);    
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

        $_url = $adminPath . '/' . $moduleName;
        if($request->is($_url .'*') && !CRUDBooster::canView()) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($_url.'/add*') && !CRUDBooster::canCreate()) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($_url.'/edit*') && !CRUDBooster::canUpdate()) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_edit',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($_url.'/delete*') && !CRUDBooster::canDelete()) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_delete',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        if ($request->is($_url.'/detail*') && !CRUDBooster::canRead()) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
                CRUDBooster::denyAccess();
        }

        return $next($request);
    }
}
