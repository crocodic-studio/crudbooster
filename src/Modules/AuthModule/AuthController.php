<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\helpers\Mailer;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use crocodicstudio\crudbooster\helpers\CRUDBooster, CB;

class AuthController extends Controller
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

    /**
     * @param string $tableName
     * @return mixed
     */
    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;
        return \DB::table($tableName);
    }

    public function getLockscreen()
    {
        if (! auth('cbAdmin')->id()) {
            Session::flush();
            return redirect()->route('getLogin')->with('message', cbTrans('alert_session_expired'));
        }

        Session::put('admin_lock', 1);
        return view('CbAuth::lockscreen');
    }

    public function postUnlockScreen()
    {
        $user = (auth('cbAdmin')->user());

        if (\Hash::check(request('password'), $user->password)) {
            Session::put('admin_lock', 0);

            return redirect()->route('CbDashboard');
        }
        echo "<script>alert('".cbTrans('alert_password_wrong')."');history.go(-1);</script>";
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

        CRUDBooster::insertLog(trans('crudbooster_logging.log_forgot', ['email' => request('email'), 'ip' => Request::server('REMOTE_ADDR')]));

        return redirect()->route('getLogin')->with('message', cbTrans('message_forgot_password'));
    }

    public function getLogout()
    {
        CRUDBooster::insertLog(trans('crudbooster_logging.log_logout', ['email' => auth('cbAdmin')->user()->email]));
        auth('cbAdmin')->logout();

        return redirect()->route('getLogin')->with('message', cbTrans('message_after_logout'));
    }

    private function validateForgotPass()
    {
        $validator = Validator::make(request()->all(), ['email' => 'required|email|exists:cms_users',]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();
            backWithMsg(implode(', ', $message), 'danger');
        }
    }
}
