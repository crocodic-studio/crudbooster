<?php
namespace crocodicstudio\crudbooster\widgets;

class PrivilegeTable
{
    public $template = 'crudbooster::_privileges.table';
    public $cacheLifeTime = 0;
    public function data($roleId)
    {
        $modules = \DB::table("cms_moduls")->where('is_protected',0)->select("cms_moduls.*")->orderby("name","asc")->get(['id','name']);

        foreach ($modules as $module){
            $module->privilege = \DB::table('cms_privileges_roles')
                ->where('id_cms_moduls', $module->id)
                ->where('id_cms_privileges', $roleId)
                ->first() ?: new \stdClass();
        }

        return $module;
    }
}