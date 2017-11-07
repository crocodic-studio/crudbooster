<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule\widgets;

class ActiveMenus
{
    public $template = 'CbMenu::_menus_management.active_menus';

    public $cacheLifeTime = 0;

    public $contextAs = '$menu_active';

    public function data()
    {
        $menu_active = app('db')->table('cms_menus')->where('parent_id', 0)->where('is_active', 1)->orderby('sorting', 'asc')->get();

        foreach ($menu_active as $menu) {
            $child = app('db')->table('cms_menus')->where('is_active', 1)->where('parent_id', $menu->id)->orderby('sorting', 'asc')->get();
            if (count($child)) {
                $menu->children = $child;
            }
        }

        return $menu_active;
    }
}