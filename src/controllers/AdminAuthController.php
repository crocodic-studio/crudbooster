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

        $themeView = getThemePath("login");
        $loginView = str_replace(".blade.php", "", getSetting('login_page_view',$themeView));
        return view($loginView, $data );
    }

    private function loginNotification() {
        if($isLoginNotification = getSetting("LOGIN_NOTIFICATION")) {
            $user = auth()->user();
            if($user->ip_address && $user->user_agent) {

                if( request()->ip() != $user->ip_address || request()->userAgent() != $user->user_agent) {
                    $code = Str::random(5);
                    session(["login_verification_code"=>$code,"last_id_attempt"=>auth()->id()]);
                    $mail = new MailHelper();
                    $mail->to($user->email);
                    $mail->sender("noreply@".$_SERVER['SERVER_NAME'],cb()->getAppName());
                    $mail->subject("Please verify that it's you?");
                    $mail->content("
                    Hi $user->name,<br/><br>
                    
                    Your sign in attempt seems a little different than usual. This could be because you are signing in from a different device or a different location.<br/>

                    If you are attempting to sign-in, please use the following code to confirm your identity:<br/>
                    $code <br/>
                    <br/><br/>
                    Here the details: <br/>
                    ".date("l, F d/m/Y")." at ".date("H:i")."<br/>
                    Account: ".$user->email."<br/>
                    IP Address: ".request()->ip()."<br/>
                    Browser: ".request()->userAgent()."<br/>
                    
                    <br/>
                    If this wasn't you, please reset your password.
                    
                    <br><br>
                    Thank you <br/>
                    ".cb()->getAppName()."
                ");
                    $mail->send();

                    auth()->logout();

                    return true;
                }
            }
        }

        return false;
    }

    public function getLoginVerification() {
        return view(getThemePath("login_verification"));
    }

    public function postSubmitLoginVerification() {
        try {
            cb()->validation(["code"]);

            if(session()->has("login_verification_code")) {
                $sessCode = session("login_verification_code");
                if(request("code") == $sessCode && session("last_id_attempt")) {
                    // Login
                    auth()->loginUsingId(session("last_id_attempt"));

                    // Update Login At
                    cb()->update("users", auth()->id(), [
                        "login_at"=>now()->format("Y-m-d H:i:s"),
                        "ip_address"=>request()->ip(),
                        "user_agent"=>request()->userAgent()
                    ]);

                    // When login user success, clear suspend attempt
                    $this->clearSuspendAttempt();

                    // Clear verification session
                    session()->forget(["last_id_attempt","login_verification_code"]);

                    cbHook()->hookPostLogin();

                    return redirect(cb()->getAdminUrl());
                }
            }

            return cb()->redirectBack("The code is invalid!","warning");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
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

                // Check if login notification is enabled
                if($this->loginNotification()) {
                    return cb()->redirect(route("AdminAuthControllerGetLoginVerification"),"We have sent you a code verification, please enter to this box");
                }

                // Update Login At
                cb()->update("users", auth()->id(), [
                   "login_at"=>now()->format("Y-m-d H:i:s"),
                   "ip_address"=>request()->ip(),
                   "user_agent"=>request()->userAgent()
                ]);

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
