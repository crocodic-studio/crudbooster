<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class DynamicMenus
{
    private $superAdminMenus = [];

    public function addSuperAdminMenu(string $partial)
    {
        return $this->superAdminMenus[] = $partial;
    }

    public function getSuperAdminMenus()
    {
        return $this->superAdminMenus;
    }
}