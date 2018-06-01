<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule\Listeners;

use crocodicstudio\crudbooster\CBCoreModule\CbUser;
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
            self::insertLog('Password Recovery Requested for '. $email);
        });
    }

    private static function logOut()
    {
        Event::listen('cb.userLoggedOut', function (CbUser $user) {
            self::insertLog('Logout '.$user->email);
        });
    }

    private static function logIn()
    {
        Event::listen('cb.userLoggedIn', function (CbUser $user, $time, $ip) {
            self::insertLog('Login '.$user->email);
        });
    }

    private static function insertLog($description, $userId = null)
    {
        LogsRepository::insertLog('crudbooster: '.$description, $userId ?: auth('cbAdmin')->id());
    }

    private static function illegalAccessTry()
    {
        Event::listen('cb.unauthorizedTryToSuperAdminArea', function (CbUser $user, $fullUrl) {
            self::insertLog('Warning - Try To Access Unauthorized Super Admin Area at: '. $fullUrl, $user->id);
        });

        Event::listen('cb.unauthorizedTryStopped', function (CbUser $user, $fullUrl) {
            self::insertLog('Warning - Try To Access Unauthorized Resource at: '. $fullUrl, $user->id);
        });

    }
}