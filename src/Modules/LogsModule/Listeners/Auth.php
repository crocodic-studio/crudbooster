<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule\Listeners;

use crocodicstudio\crudbooster\CBCoreModule\CbUser;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use crocodicstudio\crudbooster\Modules\LogsModule\LogsRepository;
use Illuminate\Support\Facades\Event;

class Auth
{
    public static function registerListeners()
    {
        self::forgottenPass();
        self::logOut();
        self::logIn();
        self::illegalAccessTry();
    }

    private static function forgottenPass()
    {
        Event::listen('cb.forgottenPasswordRequested', function (string $email, string $ip) {
            self::insertLog(trans('crudbooster_logging.log_forgot', ['email' => $email, 'ip' => $ip]));
        });
    }

    private static function logOut()
    {
        Event::listen('cb.userLoggedOut', function (CbUser $user) {
            self::insertLog(trans('crudbooster_logging.log_logout', ['email' => $user->email]));
        });
    }

    private static function logIn()
    {
        Event::listen('cb.userLoggedIn', function (CbUser $user, $time, $ip) {
            self::insertLog(trans('crudbooster_logging.log_login', ['email' => $user->email, 'ip' => $ip]));
        });
    }

    private static function insertLog($description, $id = null)
    {
        LogsRepository::insertLog('crudbooster: '.$description, $id ?: auth('cbAdmin')->id());
    }

    private static function illegalAccessTry()
    {
        Event::listen('cb.illegalTryToSuperAdminArea', function (CbUser $user, $fullUrl) {
            self::insertLog(trans("crudbooster_logging.log_illegal_try", ['url' => $fullUrl]), $user->id);
        });
    }
}