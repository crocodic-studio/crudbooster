<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

trait GetCurrentX
{
    public static function getCurrentModule()
    {
        return GetCurrentX::getCurrentModule();
    }

    public static function getCurrentMenuId()
    {
        return GetCurrentX::getCurrentMenuId();
    }

    public static function getCurrentId()
    {
        return GetCurrentX::getCurrentId();
    }

    public static function getCurrentMethod()
    {
        return GetCurrentX::getCurrentMethod();
    }

}