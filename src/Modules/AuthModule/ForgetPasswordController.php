<?php

namespace Crocodicstudio\Crudbooster\Modules\AuthModule;

use Crocodicstudio\Crudbooster\CBCoreModule\CbUsersRepo;
use Crocodicstudio\Crudbooster\controllers\Controller;
use Crocodicstudio\Crudbooster\Modules\EmailTemplates\Mailer;

class ForgetPasswordController extends Controller
{
    private $usersRepo;

    public function __construct(CbUsersRepo $usersRepo)
    {
        $this->usersRepo = $usersRepo;
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
        $this->usersRepo->updateByMail(request('email'), ['password' => bcrypt($randString)]);

        //$appname = cbGetsetting('appname');
        $user = $this->usersRepo->findByMail(request('email'));
        $user->password = $randString;
        (new Mailer())->send(['to' => $user->email, 'data' => $user, 'template' => 'forgot_password_backend']);

        event('cb.forgottenPasswordRequested', [request('email'), request()->ip()]);
        return redirect()->route('getLogin')->with('message', cbTrans('message_forgot_password'));
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
