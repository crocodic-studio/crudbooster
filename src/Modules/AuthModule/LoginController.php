<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use App\Http\Controllers\CBHook;
use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * @var \crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo
     */
    private $usersRepo;

    /**
     * AuthController constructor.
     *
     * @param \crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo $usersRepo
     */
    public function __construct(CbUsersRepo $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function postLogin()
    {
        $this->validateLogin();

        $password = request("password");

        $user = $this->usersRepo->findByMail(request("email"));

        $this->validatePassword($password, $user);

        $this->setSession($user);

        $this->LogIt($user);

        (new CBHook)->afterLogin();

        return redirect(CRUDBooster::adminPath());
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

    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;

        return \DB::table($tableName);
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

    /**
     * @param $users
     */
    private function LogIt($users)
    {
        CRUDBooster::insertLog(trans('crudbooster_logging.log_login', ['email' => $users->email, 'ip' => Request::server('REMOTE_ADDR')]));
    }

    /**
     * @param $user
     * @param $priv
     * @param $photo
     * @param $roles
     */
    private function setSession($user)
    {
        $session = [
            'admin_id' => $user->id,
            'admin_is_superadmin' => $this->table('cms_privileges')->where('id', $user->id_cms_privileges)->first()->is_superadmin,
            'admin_name' => $user->name,
            'admin_photo' => ($user->photo) ? asset($user->photo) : asset('vendor/crudbooster/avatar.jpg'),
            'admin_privileges_roles' => $this->fetchRoles($user),
            'admin_privileges' => $user->id_cms_privileges,
            'admin_privileges_name' => $this->table('cms_privileges')->where('id', $user->id_cms_privileges)->first()->name,
            'admin_lock' => 0,
            'theme_color' => $this->table('cms_privileges')->where('id', $user->id_cms_privileges)->first()->theme_color,
            'appname' => cbGetsetting('appname'),
        ];
        session($session);
    }
}