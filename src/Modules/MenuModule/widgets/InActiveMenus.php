<?php

namespace Crocodicstudio\Crudbooster\Modules\MenuModule\widgets;

use Crocodicstudio\Crudbooster\Modules\MenuModule\MenuRepo;

class InActiveMenus
{
    public $template = 'CbMenu::_menus_management.inactive_menus';

    public $cacheLifeTime = 0;

    public $contextAs = '$menu_inactive';

    public function data()
    {
        return MenuRepo::fetchMenuWithChilds(0);
    }
}