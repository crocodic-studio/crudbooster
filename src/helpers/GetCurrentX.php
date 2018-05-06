<?php

namespace crocodicstudio\crudbooster\helpers;

use DB;
use Request;
use Route;
use Session;

class GetCurrentX
{
    public static function getCurrentId() : int
    {
        $id = session('current_row_id') ?: Request::segment(4);

        return intval($id);
    }

    public static function getCurrentMethod() : string
    {
        return str_after(Route::currentRouteAction(), "@");
    }

    public static function getCurrentModule()
    {
        $modulepath = self::getModulePath();
        cache()->remember('crudbooster_modules_'.$modulepath, 2, function () use ($modulepath) {
            return DB::table('cms_moduls')->where('path', $modulepath)->first();
        });
    }

    public static function getCurrentDashboardId()
    {
        if (request('d') == null) {
            return session('currentDashboardId');
        }
        Session::put('currentDashboardId', request('d'));
        Session::put('currentMenuId', 0);

        return request('d');
    }

    public static function getCurrentMenuId()
    {
        if (request('m') == null) {
            return session('currentMenuId');
        }
        Session::put('currentMenuId', request('m'));
        Session::put('currentDashboardId', 0);

        return request('m');
    }

    private static function getModulePath()
    {
        $adminPathSegments = count(explode('/', config('crudbooster.ADMIN_PATH')));

        return Request::segment(1 + $adminPathSegments);
    }
}