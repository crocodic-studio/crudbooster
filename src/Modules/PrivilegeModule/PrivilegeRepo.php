<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

class PrivilegeRepo
{
    /**
     * @param $id
     */
    public function grantAllPermissions($id)
    {
        \DB::table('cms_privileges_roles')->insert([
            'id_cms_modules' => $id,
            'id_cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'can_see_module' => 1,
            'can_create' => 1,
            'can_read' => 1,
            'can_edit' => 1,
            'can_delete' => 1,
        ]);
    }
}