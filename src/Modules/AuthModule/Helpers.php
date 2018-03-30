<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;

trait Helpers
{
    public static function me()
    {
        return CbUsersRepo::find(session('admin_id'));
    }

    public static function myName()
    {
        return session('admin_name');
    }

    public static function myPhoto()
    {
        return session('admin_photo');
    }

    public static function isLocked()
    {
        return session('admin_lock');
    }

    public static function myId()
    {
        return session('admin_id');
    }
}