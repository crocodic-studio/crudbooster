<?php

namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ModulesRepo;
use DB;
use Request;
use Route;

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
        $modulePath = self::getModulePath();
        return cache()->remember('crudbooster_modules_'.$modulePath, 2, function () use ($modulePath) {
            $module = app('CbModulesRegistery')->getModule($modulePath);
            return $module?: ModulesRepo::getByPath($modulePath);
        });
    }

    public static function getCurrentDashboardId()
    {
        if (request('d') == null) {
            return session('currentDashboardId');
        }
        session([
            'currentDashboardId' => request('d'),
            'currentMenuId' => 0,
        ]);
        return request('d');
    }

    public static function getCurrentMenuId()
    {
        if (request('m') == null) {
            return session('currentMenuId');
        }
        session([
            'currentMenuId' => request('m'),
            'currentDashboardId' => 0,
        ]);

        return request('m');
    }

    private static function getModulePath()
    {
        $adminPathSegments = count(explode('/', cbConfig('ADMIN_PATH')));

        return Request::segment(1 + $adminPathSegments);
    }
}