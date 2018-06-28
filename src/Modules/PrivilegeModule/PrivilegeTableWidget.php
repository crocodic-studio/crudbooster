<?php

namespace Crocodicstudio\Crudbooster\Modules\PrivilegeModule;

use Crocodicstudio\Crudbooster\Modules\ModuleGenerator\ModulesRepo;

class PrivilegeTableWidget
{
    public $template = 'CbPrivilege::_privileges.table';

    public $cacheLifeTime = 0;

    public function data($roleId)
    {
        $modules = ModulesRepo::getAll(['id', 'name']);

        foreach ($modules as $module) {
            $module->privilege = \DB::table('cms_roles_privileges')->where('cms_modules_id', $module->id)->where('cms_roles_id', $roleId)->first() ?: new \stdClass();
        }

        return $modules;
    }
}