<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ModulesRepo;

class PrivilegeTableWidget
{
    public $template = 'CbPrivilege::_privileges.table';

    public $cacheLifeTime = 0;

    public function data($roleId)
    {
        $modules = ModulesRepo::getAll(['id', 'name']);

        foreach ($modules as $module) {
            $module->privilege = \DB::table('cms_privileges_roles')->where('cms_modules_id', $module->id)->where('cms_privileges_id', $roleId)->first() ?: new \stdClass();
        }

        return $modules;
    }
}