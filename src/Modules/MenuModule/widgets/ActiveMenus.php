<?php

namespace Crocodicstudio\Crudbooster\Modules\MenuModule\widgets;

use Crocodicstudio\Crudbooster\Modules\MenuModule\MenuRepo;

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