<?php

namespace crocodicstudio\crudbooster\helpers;

use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Config;
use Validator;

class GetCurrentX
{
    public static function getCurrentId()
    {
        $id = intval(session('current_row_id'));
        $id = $id ?: Request::segment(4);
        $id = intval($id);

        return $id;
    }

    public static function getCurrentMethod()
    {
        $action = str_replace(ctrlNamespace(), "", Route::currentRouteAction());
        $atloc = strpos($action, '@') + 1;
        $method = substr($action, $atloc);

        return $method;
    }

    public static function getCurrentModule()
    {
        $modulepath = self::getModulePath();
        if (Cache::has('moduls_'.$modulepath)) {
            return Cache::get('moduls_'.$modulepath);
        }

        return DB::table('cms_moduls')->where('path', self::getModulePath())->first();
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
        $adminPathSegments = count(explode('/',config('crudbooster.ADMIN_PATH')));
        return Request::segment(1 + $adminPathSegments);
    }
}