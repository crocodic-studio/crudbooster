<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends CBController
{
    use DeveloperAuthController;

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

        if(env("CB_AUTO_SUSPEND_LOGIN") && Cache::get("loginFailed".$key) >= env("CB_AUTO_SUSPEND_LOGIN")) {
            Cache::put("loginSuspended".$key, true, 30);
            Cache::forget("loginFailed".$key);
            return true;
        }

        return false;
    }

    public function getLogin()
    {
        if(!auth()->guest()) return redirect(cb()->getAdminUrl());

        cbHook()->hookGetLogin();

        return view(cbConfig('LOGIN_FORM_VIEW'));
    }

    public function postLogin()
    {
        try{
            if($this->isSuspendedLogin()) throw new CBValidationException(cbLang("you_have_been_suspended"));

            cb()->validation([
                'email'=>'required|email',
                'password'=>'required'
            ]);

            $credential = request()->only(['email','password']);
            if (auth()->attempt($credential)) {
                cbHook()->hookPostLogin();
                return redirect(cb()->getAdminUrl());
            } else {
                $this->incrementFailedLogin();
                return redirect(cb()->getLoginUrl())->with(['message'=>cbLang('password_and_username_is_wrong'),'message_type'=>'warning']);
            }
        }catch (CBValidationException $e) {
            return cb()->redirect(cb()->getAdminUrl("login"),$e->getMessage(),'warning');
        }
    }

    public function getLogout()
    {
        auth()->logout();
        return cb()->redirect(cb()->getAdminUrl("login"), cbLang('you_have_been_logged_out'), 'success');
    }


}
