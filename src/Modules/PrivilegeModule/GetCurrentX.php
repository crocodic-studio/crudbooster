<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\helpers\GetCurrentX as GetCurrentXClass;

trait GetCurrentX
{
    public static function getCurrentModule()
    {
        return GetCurrentXClass::getCurrentModule();
    }

    public static function getCurrentMenuId()
    {
        return GetCurrentXClass::getCurrentMenuId();
    }

    public static function getCurrentId()
    {
        return GetCurrentXClass::getCurrentId();
    }

    public static function getCurrentMethod()
    {
        return GetCurrentXClass::getCurrentMethod();
    }

}