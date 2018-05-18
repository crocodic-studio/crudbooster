<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

trait PrivilegeHelpers
{
    public static function isSuperadmin()
    {
        return self::findUserRole()->is_superadmin;
    }

    public static function canView()
    {
        return self::canDo('is_visible');
    }

    public static function canUpdate()
    {
        return self::canDo('is_edit');
    }

    public static function canCreate()
    {
        return self::canDo('is_create');
    }

    public static function canRead()
    {
        return self::canDo('is_read');
    }

    public static function canDelete()
    {
        return self::canDo('is_delete');
    }

    public static function canCRUD()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = session('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path !== self::getModulePath()) {
                continue;
            }
            if ($v->is_visible && $v->is_create && $v->is_read && $v->is_edit && $v->is_delete) {
                return true;
            }

            return false;
        }
    }

    private static function canDo($verb)
    {
        if (self::isSuperadmin()) {
            return true;
        }

        foreach (session('admin_privileges_roles') as $role) {
            if ($role->path == self::getModulePath()) {
                return (bool) $role->{$verb};
            }
        }
        return false;
    }

    public static function myPrivilegeId()
    {
        return session('admin_role_id');
    }

    public static function myPrivilege()
    {
        $roles = session('admin_privileges_roles');
        if (! $roles) {
            return;
        }
        foreach ($roles as $role) {
            if ($role->path == self::getModulePath()) {
                return $role;
            }
        }
    }

    private static function getModulePath()
    {
        $adminPathSegments = count(explode('/',cbConfig('ADMIN_PATH')));
        return Request::segment(1 + $adminPathSegments);
    }

    public static function myPrivilegeName()
    {
        return self::findUserRole()->name;
    }

    public static function themeColor()
    {
        return self::findUserRole()->theme_color ?: 'skin-blue';
    }

    public static function denyAccess()
    {
        static::redirect(static::adminPath(), cbTrans('denied_access'));
    }

    /**
     * @param $roleId
     * @return mixed
     */
    private static function findUserRole()
    {
        return \DB::table('cms_privileges')->where('id', self::myPrivilegeId())->first();
    }

    public static function refreshSessionRoles()
    {
        $roles = \DB::table('cms_privileges_roles')->where('id_cms_privileges', self::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        session()->put('admin_privileges_roles', $roles);
    }
}