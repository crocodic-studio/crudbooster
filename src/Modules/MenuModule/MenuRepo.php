<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use CRUDBooster;
use Illuminate\Support\Facades\DB;

class MenuRepo
{
    public static function sidebarMenu()
    {
        $menu_active = DB::table('cms_menus')
            ->where('cms_privileges', CRUDBooster::myPrivilegeId())
            ->where('parent_id', 0)->where('is_active', 1)
            ->where('is_dashboard', 0)
            ->orderby('sorting', 'asc')
            ->select('cms_menus.*')
            ->get();

        foreach ($menu_active as &$menu) {

            $url = self::menuUrl($menu);

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");

            $child = DB::table('cms_menus')
                ->where('is_dashboard', 0)
                ->where('is_active', 1)
                ->where('cms_privileges', 'like', '%"'.CRUDBooster::myPrivilegeName().'"%')
                ->where('parent_id', $menu->id)
                ->select('cms_menus.*')
                ->orderby('sorting', 'asc')
                ->get();

            if (count($child)) {
                foreach ($child as &$c) {
                    $url = self::menuUrl($c);
                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }

                $menu->children = $child;
            }
        }

        return $menu_active;
    }

    private static function menuUrl($menu)
    {
        $menu->is_broken = false;
        $menuType = $menu->type;
        if ($menuType == MenuTypes::route) {
            return route($menu->path);
        }

        if ($menuType == MenuTypes::url) {
            return $menu->path;
        }

        if ($menuType == MenuTypes::ControllerMethod) {
            return action($menu->path);
        }

        if (in_array($menuType, [MenuTypes::Module, MenuTypes::Statistic])) {
            return CRUDBooster::adminPath($menu->path);
        }

        $menu->is_broken = true;

        return '#';
    }

    public static function sidebarDashboard()
    {
        $menu = DB::table('cms_menus')->where('cms_privileges', CRUDBooster::myPrivilegeId())
            ->where('is_dashboard', 1)
            ->where('is_active', 1)->first() ?: new \stdClass();

        $menu->url = self::menuUrl($menu);

        return $menu;
    }
}