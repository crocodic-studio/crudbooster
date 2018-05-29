<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

class PrivilegeRepo
{
    public static function deleteByRoleId($id)
    {
        return \DB::table('cms_roles_privileges')->where('cms_roles_id', $id)->delete();
    }

    /**
     * @param $id
     */
    public function grantAllPermissions($id)
    {
        \DB::table('cms_roles_privileges')->insert([
            'cms_modules_id' => $id,
            'cms_roles_id' => cbUser()->cms_roles_id,
            'can_see_module' => 1,
            'can_create' => 1,
            'can_read' => 1,
            'can_edit' => 1,
            'can_delete' => 1,
        ]);
    }
}