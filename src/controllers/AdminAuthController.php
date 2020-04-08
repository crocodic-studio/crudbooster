<?php namespace crocodicstudio\crudbooster\controllers;

use App\Http\Controllers\CBHook;
use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class AdminAuthController extends BaseController
{
    public function getLockscreen()
    {
        if(cb()->auth()->guest()) return redirect_to(cb()->adminPath('login'), trans('crudbooster.alert_session_expired'), 'warning');

        cb()->auth()->lock();

        return view('crudbooster::lockscreen');
    }

    public function postUnlockScreen()
    {
        try {
            cb()->validation(['password']);

            if (\Hash::check(\request('password'), cb()->auth()->password())) {

                cb()->auth()->unlock();

                return redirect(cb()->adminPath());
            } else {
                echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";
            }

        } catch (\CBValidationException $e) {
            echo "<script>alert('".$e->getMessage()."');history.go(-1);</script>";
        }
    }

    public function getLogin()
    {
        if(!cb()->auth()->guest()) return redirect(cb()->adminPath());

        return view('crudbooster::login');
    }

    public function postLogin()
    {
        try {
            cb()->validation([config('crudbooster.LOGIN_ID_COLUMN.column','email')=>'required|exists:'.config('crudbooster.USER_TABLE'),config('crudbooster.LOGIN_PASS_COLUMN.column','password')=>'required']);

            if(cb()->auth()->attempt(\request(config('crudbooster.LOGIN_ID_COLUMN.column','email')), \request(config('crudbooster.LOGIN_PASS_COLUMN.column','password')))) {

                CBHook::afterLogin();

                return redirect(cb()->adminPath());
            } else {
                return redirect_back(trans('crudbooster.alert_password_wrong'),'warning');
            }

        } catch (CBValidationException $e) {
            return redirect_back($e->getMessage(),'warning');
        }
    }

    public function getForgot()
    {
        if(!cb()->auth()->guest()) return redirect(cb()->adminPath());

        return view('crudbooster::forgot');
    }

    public function postForgot()
    {
        try {
            cb()->validation(['email'=>'required|email|exists:'.config('crudbooster.USER_TABLE')]);

            $rand_string = str_random(5);
            $password = \Hash::make($rand_string);

            DB::table(config('crudbooster.USER_TABLE'))->where('email', Request::input('email'))->update(['password' => $password]);

            $appname = cb()->getSetting('appname');
            $user = cb()->first(config('crudbooster.USER_TABLE'), ['email' => g('email')]);
            $user->password = $rand_string;
            cb()->sendEmail(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

            cb()->insertLog(trans("crudbooster.log_forgot", ['email' => g('email'), 'ip' => Request::server('REMOTE_ADDR')]));

            return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));

        } catch (\CBValidationException $e) {
            return redirect_back($e->getMessage(),'warning');
        }
    }

    public function getLogout()
    {
        cb()->auth()->logout();

        CBHook::afterLogout();

        cb()->insertLog(trans("crudbooster.log_logout", ['email' => cb()->auth()->email()]));

        return redirect_to(cb()->adminPath('login'),trans("crudbooster.message_after_logout"));
    }
}
