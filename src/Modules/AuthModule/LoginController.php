<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use CB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class LoginController
{
    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;
        return \DB::table($tableName);
    }

    public function postLogin()
    {
        $this->validateLogin();

        $password = request("password");
        $user = CbUsersRepo::findByMail(request("email"));

        $this->validatePassword($password, $user);

        $priv = $this->table('cms_privileges')->where('id', $user->id_cms_privileges)->first();

        $roles = $this->fetchRoles($user);

        $photo = ($user->photo) ? asset($user->photo) : asset('vendor/crudbooster/avatar.jpg');
        session()->put('admin_id', $user->id);
        session()->put('admin_is_superadmin', $priv->is_superadmin);
        session()->put('admin_name', $user->name);
        session()->put('admin_photo', $photo);
        session()->put('admin_privileges_roles', $roles);
        session()->put('admin_privileges', $user->id_cms_privileges);
        session()->put('admin_privileges_name', $priv->name);
        session()->put('admin_lock', 0);
        session()->put('theme_color', $priv->theme_color);
        session()->put('appname', cbGetsetting('appname'));

        $this->LogIt($user);

        $cbHookSession = new \App\Http\Controllers\CBHook;
        $cbHookSession->afterLogin();

        return redirect(CB::adminPath());
    }


    private function validateLogin()
    {
        $validator = Validator::make(request()->only(['email', 'password']), [
            'email' => 'required|email|exists:cms_users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            backWithMsg(implode(', ', $message), 'danger');
        }
    }

    /**
     * @param $password
     * @param $users
     */
    private function validatePassword($password, $users)
    {
        if (! \Hash::check($password, $users->password)) {
            $resp = redirect()->route('getLogin')->with('message', cbTrans('alert_password_wrong'));
            sendAndTerminate($resp);
        }
    }

    /**
     * @param $users
     */
    private function LogIt($users)
    {
        CB::insertLog(trans('logging.log_login', ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));
    }

    /**
     * @param $user
     * @return mixed
     */
    private function fetchRoles($user)
    {
        $roles = $this->table('cms_privileges_roles')->where('id_cms_privileges', $user->id_cms_privileges)->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();

        return $roles;
    }
}