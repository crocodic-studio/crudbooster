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

    public function postLogin()
    {
        $validator = Validator::make(request()->all(), [
                'email' => 'required|email|exists:cms_users',
                'password' => 'required',
            ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            backWithMsg(implode(', ', $message), 'danger');
        }

        $password = request("password");
        $users = CbUsersRepo::findByMail(request("email"));

        if (! \Hash::check($password, $users->password)) {
            return redirect()->route('getLogin')->with('message', cbTrans('alert_password_wrong'));
        }

        $priv = $this->table('cms_privileges')->where('id', $users->id_cms_privileges)->first();

        $roles = $this->table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();

        $photo = ($users->photo) ? asset($users->photo) : asset('vendor/crudbooster/avatar.jpg');
        session()->put('admin_id', $users->id);
        session()->put('admin_is_superadmin', $priv->is_superadmin);
        session()->put('admin_name', $users->name);
        session()->put('admin_photo', $photo);
        session()->put('admin_privileges_roles', $roles);
        session()->put('admin_privileges', $users->id_cms_privileges);
        session()->put('admin_privileges_name', $priv->name);
        session()->put('admin_lock', 0);
        session()->put('theme_color', $priv->theme_color);
        session()->put('appname', cbGetsetting('appname'));

        CRUDBooster::insertLog(trans('logging.log_login', ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));

        $cbHookSession = new \App\Http\Controllers\CBHook;
        $cbHookSession->afterLogin();

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
