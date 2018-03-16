<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use CRUDBooster;

class CBBackend
{
    private $module;

    private $url;

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
        $this->module = CRUDBooster::getCurrentModule();
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

        $this->url = $adminPath . '/' . $moduleName;

        $this->guardView($request);
        $this->guardCreate($request);
        $this->guardUpdate($request);
        $this->guardDelete($request);
        $this->guardRead($request);

        return $next($request);
    }

    /**
     * @param $request
     */
    private function guardView($request)
    {
        if ($request->is($this->url.'*') && ! CRUDBooster::canView()) {
            CRUDBooster::insertLog(cbTrans('log_try_view', ['module' => $this->module->name]));
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardCreate($request)
    {
        if ($request->is($this->url.'/add*') && ! CRUDBooster::canCreate()) {
            CRUDBooster::insertLog(cbTrans('log_try_add', ['module' => $this->module->name]));
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardUpdate($request)
    {
        if ($request->is($this->url.'/edit*') && ! CRUDBooster::canUpdate()) {
            CRUDBooster::insertLog(cbTrans('log_try_edit', ['module' => $this->module->name]));
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardDelete($request)
    {
        if ($request->is($this->url.'/delete*') && ! CRUDBooster::canDelete()) {
            CRUDBooster::insertLog(cbTrans('log_try_delete', ['module' => $this->module->name]));
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardRead($request)
    {
        if ($request->is($this->url.'/detail*') && ! CRUDBooster::canRead()) {
            CRUDBooster::insertLog(cbTrans('log_try_view', ['module' => $this->module->name]));
            CRUDBooster::denyAccess();
        }
    }
}
