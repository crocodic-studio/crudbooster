<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use CRUDBooster;
use CB;

class AuthController extends Controller
{
    function getIndex()
    {
        return view('CbAuth::home', ['page_title' => '<strong>Dashboard</strong>']);
    }

    public function getLockscreen()
    {
        if (! CRUDBooster::myId()) {
            Session::flush();
            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_session_expired'));
        }

        Session::put('admin_lock', 1);
        return view('CbAuth::lockscreen');
    }

    public function postUnlockScreen()
    {
        $users = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();

        if (\Hash::check(Request::input('password'), $users->password)) {
            Session::put('admin_lock', 0);

            return redirect()->route('AuthControllerGetIndex');
        }
        echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";
    }

    public function getLogin()
    {
        if (CRUDBooster::myId()) {
            return redirect(cbAdminPath());
        }

        return view('CbAuth::login');
    }

    public function postLogin()
    {
        $validator = Validator::make(Request::all(), [
                'email' => 'required|email|exists:cms_users',
                'password' => 'required',
            ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            return CRUDBooster::backWithMsg(implode(', ', $message), 'danger');
        }

        $email = Request::input("email");
        $password = Request::input("password");
        $users = DB::table('cms_users')->where("email", $email)->first();

        if (! \Hash::check($password, $users->password)) {
            return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_password_wrong'));
        }

        $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();

        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();

        $photo = ($users->photo) ? asset($users->photo) : asset('vendor/crudbooster/avatar.jpg');
        Session::put('admin_id', $users->id);
        Session::put('admin_is_superadmin', $priv->is_superadmin);
        Session::put('admin_name', $users->name);
        Session::put('admin_photo', $photo);
        Session::put('admin_privileges_roles', $roles);
        Session::put("admin_privileges", $users->id_cms_privileges);
        Session::put('admin_privileges_name', $priv->name);
        Session::put('admin_lock', 0);
        Session::put('theme_color', $priv->theme_color);
        Session::put("appname", CRUDBooster::getSetting('appname'));

        CRUDBooster::insertLog(trans("crudbooster.log_login", ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));

        $cb_hook_session = new \App\Http\Controllers\CBHook;
        $cb_hook_session->afterLogin();

        return redirect(CRUDBooster::adminPath());
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
        $validator = Validator::make(Request::all(), ['email' => 'required|email|exists:cms_users',]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();
            return CRUDBooster::backWithMsg(implode(', ', $message), 'danger');
        }

        $rand_string = str_random(5);
        DB::table('cms_users')->where('email', Request::input('email'))->update(['password' => \Hash::make($rand_string)]);

        //$appname = CRUDBooster::getSetting('appname');
        $user = CRUDBooster::first('cms_users', ['email' => request('email')]);
        $user->password = $rand_string;
        CRUDBooster::sendEmail(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

        CRUDBooster::insertLog(trans("crudbooster.log_forgot", ['email' => request('email'), 'ip' => Request::server('REMOTE_ADDR')]));

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));
    }

    public function getLogout()
    {
        CRUDBooster::insertLog(trans("crudbooster.log_logout", ['email' => CRUDBooster::me()->email]));

        Session::flush();

        return redirect()->route('getLogin')->with('message', trans("crudbooster.message_after_logout"));
    }
}
