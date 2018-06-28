<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule;

class DynamicMenus
{
    private $superAdminMenus = [];

    private $userMenus = [];

    public function addSuperAdminMenu(string $partial)
    {
        return $this->superAdminMenus[] = $partial;
    }

    public function getSuperAdminMenus()
    {
        return $this->superAdminMenus;
    }

    public function getMenus()
    {
        return $this->userMenus;
    }

    public function addMenu(string $partial)
    {
        return $this->userMenus[] = $partial;
    }
}