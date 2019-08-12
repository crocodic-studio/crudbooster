<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Support\Facades\Cache;

class AdminAuthController extends CBController
{

    public function getLogin()
    {
        if(!auth()->guest()) return redirect(cb()->getAdminUrl());

        cbHook()->hookGetLogin();

        return view(cbConfig('LOGIN_FORM_VIEW'));
    }

    public function postLogin()
    {
        try{
            cb()->validation([
                'email'=>'required|email',
                'password'=>'required'
            ]);
            $credential = request()->only(['email','password']);
            if (auth()->attempt($credential)) {
                cbHook()->hookPostLogin();
                return redirect(cb()->getAdminUrl());
            } else {
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

    public function getLoginDeveloper() {
        return view('crudbooster::dev_layouts.login');
    }

    public function postLoginDeveloper() {
        try{
            cb()->validation([
                'username'=>'required',
                'password'=>'required'
            ]);

            if(request('username') == getSetting('developer_username')
                && request('password') == getSetting("developer_password")) {

                session(['developer'=>getSetting('developer_username')]);

                return redirect(cb()->getDeveloperUrl());

            }else{
                return cb()->redirectBack( cbLang("password_and_username_is_wrong"));
            }

        }catch (CBValidationException $e) {
            return cb()->redirect(cb()->getLoginUrl(),$e->getMessage(),'warning');
        }
    }

    public function getLogoutDeveloper() {
        session()->forget("developer");

        return redirect(cb()->getAdminUrl("login"));
    }
}
