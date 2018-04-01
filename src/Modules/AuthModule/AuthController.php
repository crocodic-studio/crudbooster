<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\helpers\Mailer;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use CRUDBooster;
use CB;

class AuthController extends Controller
{
    /**
     * @param null $tableName
     * @return mixed
     */
    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;
        return \DB::table($tableName);
    }

    function getIndex()
    {
        return view('CbAuth::home', ['page_title' => '<strong>Dashboard</strong>']);
    }

    public function getLockscreen()
    {
        if (! CRUDBooster::myId()) {
            Session::flush();
            return redirect()->route('getLogin')->with('message', cbTrans('alert_session_expired'));
        }

        Session::put('admin_lock', 1);
        return view('CbAuth::lockscreen');
    }

    public function postUnlockScreen()
    {
        $user = CbUsersRepo::find(CRUDBooster::myId());

        if (\Hash::check(request('password'), $user->password)) {
            Session::put('admin_lock', 0);

            return redirect()->route('AuthControllerGetIndex');
        }
        echo "<script>alert('".cbTrans('alert_password_wrong')."');history.go(-1);</script>";
    }

    public function getLogin()
    {
        if (CRUDBooster::myId()) {
            return redirect(cbAdminPath());
        }

        return view('CbAuth::login');
    }

    public function getForgot()
    {
        if (CRUDBooster::myId()) {
            return redirect()->action('\\'.AuthController::class.'@getIndex');
        }

        return view('CbAuth::forgot');
    }

    public function postForgot()
    {
        $validator = Validator::make(request()->all(), ['email' => 'required|email|exists:cms_users',]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();
            backWithMsg(implode(', ', $message), 'danger');
        }

        $randString = str_random(5);
        CbUsersRepo::updateByMail(request('email'), ['password' => \Hash::make($randString)]);

        //$appname = cbGetsetting('appname');
        $user = CbUsersRepo::findByMail(request('email'));
        $user->password = $randString;
        (new Mailer())->send(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

        CRUDBooster::insertLog(cbTrans('log_forgot', ['email' => request('email'), 'ip' => Request::server('REMOTE_ADDR')]));

        return redirect()->route('getLogin')->with('message', cbTrans('message_forgot_password'));
    }

    public function getLogout()
    {
        CRUDBooster::insertLog(cbTrans('log_logout', ['email' => CRUDBooster::me()->email]));
        Session::flush();

        return redirect()->route('getLogin')->with('message', cbTrans('message_after_logout'));
    }
}
