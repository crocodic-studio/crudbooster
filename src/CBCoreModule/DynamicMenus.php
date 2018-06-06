<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

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

    public function getSMenus()
    {
        return $this->userMenus;
    }

    public function addMenu(string $partial)
    {
        return $this->userMenus[] = $partial;
    }
}