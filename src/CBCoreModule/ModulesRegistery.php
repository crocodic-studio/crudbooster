<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class ModulesRegistery
{
    private $modules = [];

    public function addModule($key, $module)
    {
        $this->modules[$key] = $module;
    }

    public function getModule($key)
    {
        return $this->modules[$key] ?? null;
    }
}