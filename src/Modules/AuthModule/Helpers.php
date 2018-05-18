<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

trait Helpers
{
    public static function isLocked()
    {
        return session('admin_lock');
    }
}