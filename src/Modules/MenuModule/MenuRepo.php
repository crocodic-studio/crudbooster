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

            $url = CRUDBooster::menuUrl($menu);

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
                    $url = CRUDBooster::menuUrl($c);
                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }

                $menu->children = $child;
            }
        }

        return $menu_active;
    }
}