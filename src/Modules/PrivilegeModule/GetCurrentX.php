<?php

namespace Crocodicstudio\Crudbooster\Modules\PrivilegeModule;

use Crocodicstudio\Crudbooster\Helpers\GetCurrentX as GetCurrentXClass;

trait GetCurrentX
{
    public static function getCurrentModule()
    {
        return GetCurrentXClass::getCurrentModule();
    }

    public static function getCurrentModuleName()
    {
        return GetCurrentXClass::getCurrentModule()->name;
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