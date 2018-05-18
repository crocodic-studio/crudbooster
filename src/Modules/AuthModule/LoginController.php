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
        $cred = request()->only(['email', 'password']);
        $this->validateLogin($cred);

        $user = $this->usersRepo->findByMail(request("email"));

        if (! auth('cbAdmin')->attempt($cred)) {
            $resp = redirect()->route('getLogin')->with('message', cbTrans('alert_password_wrong'));
            sendAndTerminate($resp);
        }

        $this->setSession($user);

        $this->LogIt($user);

        (new CBHook)->afterLogin();

        return redirect(CRUDBooster::adminPath());
    }

    private function validateLogin($cred)
    {
        $validator = \Validator::make($cred, [
            'email' => 'required|email|exists:cms_users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();

            backWithMsg(implode(', ', $message), 'danger');
        }
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
            'admin_role_id' => $user->id_cms_privileges,
            'admin_lock' => 0,
        ];
        session($session);
        CRUDBooster::refreshSessionRoles();
    }

    /**
     * @param $users
     */
    private function LogIt($users)
    {
        CRUDBooster::insertLog(trans('crudbooster_logging.log_login', ['email' => $users->email, 'ip' => request()->server('REMOTE_ADDR')]));
    }

    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;

        return \DB::table($tableName);
    }
}