<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;

class MenuRepo
{
    public static function sidebarMenu()
    {
       $conditions = [
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'parent_id' => 0,
            'is_active' => 1,
            'is_dashboard'=> 0
        ];
        $menuActive = self::where($conditions)->orderby('sorting', 'asc')->get();

        foreach ($menuActive as &$menu) {

            $url = self::menuUrl($menu);

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");
            $conditions = [
                ['is_dashboard', '=', 0],
                ['is_active', '=', 1],
                ['parent_id', '=', $menu->id],
                ['cms_privileges', 'like', '%"'.auth('cbAdmin')->user()->role()->name.'"%'],
            ];
            $child = self::where($conditions)->orderby('sorting', 'asc')->get();

            if (count($child)) {
                foreach ($child as &$c) {
                    $url = self::menuUrl($c);
                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }
            }
            $menu->children = $child;
        }

        return $menuActive;
    }

    private static function table()
    {
        return DB::table('cms_menus');
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
        $conditions = [
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'is_dashboard' => 1,
            'is_active' => 1,
        ];
        $menu = self::where($conditions)->first() ?: new \stdClass();

        $menu->url = self::menuUrl($menu);

        return $menu;
    }

    public static function fetchMenuWithChilds($status = 1)
    {
        $menus = self::fetchMenu(0, $status);

        foreach ($menus as $menu) {
            $child = self::fetchMenu($menu->id, $status);
            if (count($child)) {
                $menu->children = $child;
            }
        }

        return $menus;
    }

    public static function fetchMenu($parent, $status = 1)
    {
        $conditions = [
            'parent_id' => $parent,
            'is_active' => $status,
        ];
        return self::where($conditions)->orderby('sorting', 'asc')->get();
    }

    public static function getDashboard()
    {
        return self::where(['is_dashboard' => 1])->first();
    }

    private static function where($conditions)
    {
        return self::table()->where($conditions);
    }
}