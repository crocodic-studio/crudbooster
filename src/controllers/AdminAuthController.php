<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\MailHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminAuthController extends CBController
{
    use DeveloperAuthController, AuthSuspend, ForgetController, RegisterController;


    public function getRedirectToLogin() {
        return redirect(cb()->getAdminUrl("login"));
    }

    public function getLogin()
    {
        if(!auth()->guest()) return redirect(cb()->getAdminUrl());

        cbHook()->hookGetLogin();

        $data = [];
        $data['no1'] = rand(1,10);
        $data['no2'] = rand(1,10);
        Session::put("captcha_result", $data['no1']+$data['no2']);

        return view( str_replace(".blade.php", "", getSetting('login_page_view','crudbooster::login')), $data );
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

                // When login user success, clear suspend attempt
                $this->clearSuspendAttempt();

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
