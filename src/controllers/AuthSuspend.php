<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/14/2019
 * Time: 8:44 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Cache;

trait AuthSuspend
{

    private function clearSuspendAttempt() {
        $key = md5(request()->ip().request()->userAgent());
        Cache::forget("loginFailed".$key);
    }

    private function incrementFailedLogin()
    {
        $key = md5(request()->ip().request()->userAgent());
        Cache::increment("loginFailed".$key, 1);
    }

    private function isSuspendedLogin()
    {
        $key = md5(request()->ip().request()->userAgent());

        if(Cache::has("loginSuspended".$key)) {
            return true;
        }

        if(getSetting("AUTO_SUSPEND_LOGIN") && Cache::get("loginFailed".$key) >= getSetting("AUTO_SUSPEND_LOGIN")) {
            Cache::put("loginSuspended".$key, true, 30);
            $this->clearSuspendAttempt();
            return true;
        }

        return false;
    }

}