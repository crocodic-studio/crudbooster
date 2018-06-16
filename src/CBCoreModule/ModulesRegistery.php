<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class ModulesRegistery
{
    private $modules = [];

    public function addModule($key, $module)
    {
        if (array_key_exists($key, $this->modules)) {
            throw new \Exception("$key already exists.");
        }
        $this->modules[$key] = $module;
        return $this->modules;
    }

    public function getModule($key)
    {
        return $this->modules[$key] ?? null;
    }
}