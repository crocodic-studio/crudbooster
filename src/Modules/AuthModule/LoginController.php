<?php

namespace Crocodicstudio\Crudbooster\Modules\AuthModule;

use Crocodicstudio\Crudbooster\CBCoreModule\CbUsersRepo;
use Crocodicstudio\Crudbooster\helpers\CRUDBooster;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    /**
     * @var \Crocodicstudio\Crudbooster\CBCoreModule\CbUsersRepo
     */
    private $usersRepo;

    /**
     * AuthController constructor.
     *
     * @param \Crocodicstudio\Crudbooster\CBCoreModule\CbUsersRepo $usersRepo
     */
    public function __construct(CbUsersRepo $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function postLogin()
    {
        $credentials = request()->only(['email', 'password']);
        $this->validateLogin($credentials);

        $user = $this->usersRepo->findByMail(request("email"));

        if (! auth('cbAdmin')->attempt($credentials)) {
            $resp = redirect()->route('getLogin')->with('message', cbTrans('alert_password_wrong'));
            respondWith($resp);
        }

        CRUDBooster::refreshSessionRoles();
        event('cb.userLoggedIn', [$user, YmdHis(), request()->ip()]);

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

    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;

        return \DB::table($tableName);
    }
}