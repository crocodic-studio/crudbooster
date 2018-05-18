<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use App\Http\Controllers\CBHook;
use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Routing\Controller;

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

        $user = $this->usersRepo->findByMail(request("email"));

        $this->validatePassword(request("password"), $user->password);

        $this->setSession($user);

        $this->LogIt($user);

        (new CBHook)->afterLogin();

        return redirect(CRUDBooster::adminPath());
    }

    private function validateLogin()
    {
        $validator = \Validator::make(request()->only(['email', 'password']), [
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
     * @param $realPassword
     */
    private function validatePassword($password, $realPassword)
    {
        if (! \Hash::check($password, $realPassword)) {
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
     * @param $users
     */
    private function LogIt($users)
    {
        CRUDBooster::insertLog(trans('crudbooster_logging.log_login', ['email' => $users->email, 'ip' => request()->server('REMOTE_ADDR')]));
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
            'admin_name' => $user->name,
            'admin_photo' => $user->photo,
            'admin_role_id' => $user->id_cms_privileges,
            'admin_lock' => 0,
        ];
        session($session);
        CRUDBooster::refreshSessionRoles();
    }
}