<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/3/2020
 * Time: 6:38 PM
 */

namespace crocodicstudio\crudbooster\helpers;


use crocodicstudio\crudbooster\models\CmsUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Auth
{
    public function lock() {
        session()->put('admin_lock',1);
    }

    public function unlock() {
        session()->forget('admin_lock');
    }

    public function refreshRole() {
        $roles = DB::table('cms_privileges_roles')
            ->where('id_cms_privileges', cb()->myPrivilegeId())
            ->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')
            ->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')
            ->get();
        session()->put('admin_privileges_roles', $roles);
    }

    public function attempt($email, $password) {
        $password_column = config('crudbooster.LOGIN_PASS_COLUMN.column','password');
        $users = DB::table(config('crudbooster.USER_TABLE'))->where(config('crudbooster.LOGIN_ID_COLUMN.column','email'), $email)->first();
        if($users && Hash::check($password, $users->{$password_column})) {
            $priv = DB::table("cms_privileges")->where("id", $users->id_cms_privileges)->first();
            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', $users->id_cms_privileges)
                ->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')
                ->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')
                ->get();
            $photo = ($users->photo) ? asset($users->photo) : cb()->dummyPhoto();
            session()->put('admin_id', $users->id);
            session()->put('admin_is_superadmin', $priv->is_superadmin);
            session()->put('admin_name', $users->name);
            session()->put('admin_email', $users->email);
            session()->put('admin_photo', $photo);
            session()->put('admin_privileges_roles', $roles);
            session()->put("admin_privileges", $users->id_cms_privileges);
            session()->put('admin_privileges_name', $priv->name);
            session()->put('admin_lock', 0);
            session()->put('theme_color', $priv->theme_color);
            session()->put("appname", cb()->getSetting('appname'));

            return true;
        }

        return false;
    }

    public function roleId() {
        return session('admin_privileges');
    }

    public function roleName() {
        return session('admin_privileges_name');
    }

    public function logout() {
        session()->forget('admin_id');
        session()->forget('admin_is_superadmin');
        session()->forget('admin_name');
        session()->forget('admin_email');
        session()->forget('admin_photo');
        session()->forget('admin_privileges_roles');
        session()->forget("admin_privileges");
        session()->forget('admin_privileges_name');
        session()->forget('admin_lock');
        session()->forget('theme_color');
        session()->forget("appname");
    }

    /**
     * @return CmsUsers
     */
    public function user() {
        return CmsUsers::findById($this->id());
    }

    public function name() {
        return session('admin_name');
    }

    public function password() {
        return DB::table(config('crudbooster.USER_TABLE'))->where('id', $this->id())->first()->password;
    }

    public function email() {
        return session('admin_email');
    }

    public function id() {
        return session('admin_id');
    }

    public function guest() {
        if($this->id()) return false;
        else return true;
    }
}