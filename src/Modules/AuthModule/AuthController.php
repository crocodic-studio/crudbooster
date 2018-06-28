<?php

namespace Crocodicstudio\Crudbooster\Modules\AuthModule;

use Crocodicstudio\Crudbooster\CBCoreModule\CbUsersRepo;
use Crocodicstudio\Crudbooster\controllers\Controller;
use Crocodicstudio\Crudbooster\Modules\EmailTemplates\Mailer;

class AuthController extends Controller
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

    /**
     * @param string $tableName
     * @return mixed
     */
    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;
        return \DB::table($tableName);
    }

    public function getLogin()
    {
        if (auth('cbAdmin')->id()) {
            return redirect(cbAdminPath());
        }

        return view('CbAuth::login');
    }

    public function getForgot()
    {
        if (auth('cbAdmin')->id()) {
            return redirect()->route('CbDashboard');
        }

        return view('CbAuth::forgot');
    }

    public function postForgot()
    {
        $this->validateForgotPass();

        $randString = str_random(5);
        $this->usersRepo->updateByMail(request('email'), ['password' => \Hash::make($randString)]);

        //$appname = cbGetsetting('appname');
        $user = $this->usersRepo->findByMail(request('email'));
        $user->password = $randString;
        (new Mailer())->send(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

        event('cb.forgottenPasswordRequested', [request('email'), request()->ip()]);
        return redirect()->route('getLogin')->with('message', cbTrans('message_forgot_password'));
    }

    public function getLogout()
    {
        event('cb.userLoggedOut', [cbUser()]);
        auth('cbAdmin')->logout();

        return redirect()->route('getLogin')->with('message', cbTrans('message_after_logout'));
    }

    private function validateForgotPass()
    {
        $validator = \Validator::make(request()->all(), ['email' => 'required|email|exists:cms_users',]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();
            backWithMsg(implode(', ', $message), 'danger');
        }
    }
}
