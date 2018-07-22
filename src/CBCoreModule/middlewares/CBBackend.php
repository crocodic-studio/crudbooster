<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\middlewares;

use Closure;
use Crocodicstudio\Crudbooster\Helpers\CRUDBooster;

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

        if (auth('cbAdmin')->guest()) {
            return redirect(url($adminPath.'/login'))->with('message', cbTrans('not_logged_in'));
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
            $this->stopIllegalAction('view');
        }
    }

    /**
     * @param $request
     */
    private function guardCreate($request)
    {
        if ($request->is($this->url.'/add*') && ! CRUDBooster::canCreate()) {
            $this->stopIllegalAction('add');
        }
    }

    /**
     * @param $request
     */
    private function guardUpdate($request)
    {
        if ($request->is($this->url.'/edit*') && ! CRUDBooster::canUpdate()) {
            $this->stopIllegalAction('edit');
        }
    }

    /**
     * @param $request
     */
    private function guardDelete($request)
    {
        if ($request->is($this->url.'/delete*') && ! CRUDBooster::canDelete()) {
            $this->stopIllegalAction('delete');
        }
    }

    /**
     * @param $request
     */
    private function guardRead($request)
    {
        if ($request->is($this->url.'/detail*') && ! CRUDBooster::canRead()) {
            $this->stopIllegalAction('view');
        }
    }

    /**
     * @param $action
     */
    private function stopIllegalAction($action)
    {
        event('cb.unauthorizedTryStopped', [cbUser(), $action]);
        CRUDBooster::denyAccess();
    }
}
