<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class DynamicMenus
{
    private $menus = [];

    public function addMenu(string $partial)
    {
        return $this->menus[] = $partial;
    }

    public function getMenus()
    {
        return $this->menus;
    }
}