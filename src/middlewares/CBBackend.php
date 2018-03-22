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
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $adminPath = cbConfig('ADMIN_PATH', 'admin');

        if (CRUDBooster::myId() == '') {
            return redirect(url($adminPath.'/login'))->with('message', cbTrans('not_logged_in'));
        }

        if (CRUDBooster::isLocked()) {
            return redirect(url($adminPath.'/lock-screen'));
        }

        $moduleName = $request->segment(2);
        $this->module = CRUDBooster::getCurrentModule();

        foreach (['notifications', 'users/profile', 'users/edit-save'] as $e) {
            if ($request->is($adminPath.'/'.$e.'*')) {
                return $next($request);
            }
        }

        if ($request->is($adminPath)) {
            return $next($request);
        }

        $this->url = $adminPath.'/'.$moduleName;

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
            $this->logIt('log_try_view');
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardCreate($request)
    {
        if ($request->is($this->url.'/add*') && ! CRUDBooster::canCreate()) {
            $this->logIt('log_try_add');
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardUpdate($request)
    {
        if ($request->is($this->url.'/edit*') && ! CRUDBooster::canUpdate()) {
            $this->logIt('log_try_edit');
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardDelete($request)
    {
        if ($request->is($this->url.'/delete*') && ! CRUDBooster::canDelete()) {
            $this->logIt('log_try_delete');
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $request
     */
    private function guardRead($request)
    {
        if ($request->is($this->url.'/detail*') && ! CRUDBooster::canRead()) {
            $this->logIt('log_try_view');
            CRUDBooster::denyAccess();
        }
    }

    /**
     * @param $key
     */
    private function logIt($key)
    {
        CRUDBooster::insertLog(cbTrans($key, ['module' => $this->module->name]));
    }
}
