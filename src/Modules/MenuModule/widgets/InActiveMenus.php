<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule\widgets;

class InActiveMenus
{
    public $template = 'CbMenu::_menus_management.inactive_menus';

    public $cacheLifeTime = 0;

    public $contextAs = '$menu_inactive';

    public function data()
    {
        $menu_inactive = app('db')->table('cms_menus')->where('parent_id', 0)->where('is_active', 0)->orderby('sorting', 'asc')->get();

        foreach ($menu_inactive as $menu) {
            $child = app('db')->table('cms_menus')->where('is_active', 0)->where('parent_id', $menu->id)->orderby('sorting', 'asc')->get();
            if (count($child)) {
                $menu->children = $child;
            }
        }

        return $menu_inactive;
    }
}