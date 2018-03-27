<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule\widgets;

use crocodicstudio\crudbooster\Modules\MenuModule\MenuRepo;

class ActiveMenus
{
    public $template = 'CbMenu::_menus_management.active_menus';

    public $cacheLifeTime = 0;

    public $contextAs = '$menu_active';

    public function data()
    {
        return MenuRepo::fetchMenuWithChilds(1);
    }
}