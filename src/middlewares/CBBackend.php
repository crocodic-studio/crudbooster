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
        $adminPath = config('crudbooster.ADMIN_PATH')?:'admin';

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

        if(!$request->is($adminPath)) {            
            if($request->is($adminPath.'/'.$moduleName.'*')) {
                if(!CRUDBooster::canView()) {
                    CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
                    CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
                }            
            }elseif ($request->is($adminPath.'/'.$moduleName.'/add*')) {
                if(!CRUDBooster::canCreate()) {
                    CRUDBooster::insertLog(trans('crudbooster.log_try_add',['module'=>$module->name]));
                    CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));    
                }            
            }elseif ($request->is($adminPath.'/'.$moduleName.'/edit*')) {
                if(!CRUDBooster::canEdit()) {
                    CRUDBooster::insertLog(trans('crudbooster.log_try_edit',['module'=>$module->name]));
                    CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
                }            
            }elseif ($request->is($adminPath.'/'.$moduleName.'/delete*')) {
                if(!CRUDBooster::canDelete()) {
                    CRUDBooster::insertLog(trans('crudbooster.log_try_delete',['module'=>$module->name]));
                    CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
                }            
            }elseif ($request->is($adminPath.'/'.$moduleName.'/detail*')) {
                if(!CRUDBooster::canRead()) {
                    CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
                    CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
                }            
            }
        }

        return $next($request);
    }
}
