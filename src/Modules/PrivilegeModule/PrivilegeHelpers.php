<?php

namespace Crocodicstudio\Crudbooster\Modules\PrivilegeModule;

trait PrivilegeHelpers
{
    public static function canView()
    {
        return self::canDo('can_see_module');
    }

    private static function canDo($verb)
    {
        if (self::isSuperadmin()) {
            return true;
        }

        foreach (self::getPrivileges() as $role) {
            if ($role->path == self::getModulePath()) {
                return (bool) $role->{$verb};
            }
        }

        return false;
    }

    public static function isSuperadmin()
    {
        return cbUser()->role()->is_superadmin;
    }

    private static function getModulePath()
    {
        $adminPathSegments = count(explode('/', cbConfig('ADMIN_PATH')));

        return Request::segment(1 + $adminPathSegments);
    }

    public static function canUpdate()
    {
        return self::canDo('can_edit');
    }

    public static function canCreate()
    {
        return self::canDo('can_create');
    }

    public static function canRead()
    {
        return self::canDo('can_read');
    }

    public static function canDelete()
    {
        return self::canDo('can_delete');
    }

    public static function canCRUD()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        foreach (self::getPrivileges() as $role) {
            if ($role->path !== self::getModulePath()) {
                continue;
            }
            if ($role->can_see_module && $role->can_create && $role->can_read && $role->can_edit && $role->can_delete) {
                return true;
            }

            return false;
        }
    }

    public static function myPrivilege()
    {
        $roles = self::getPrivileges();

        foreach ($roles as $role) {
            if ($role->path == self::getModulePath()) {
                return $role;
            }
        }
    }

    public static function themeColor()
    {   
        $cbUser = cbUser();
        return ($cbUser)?$cbUser->role()->theme_color : 'skin-blue';
    }

    public static function denyAccess()
    {
        static::redirect(static::adminPath(), cbTrans('denied_access'));
    }

    public static function refreshSessionRoles()
    {
        return cache()->forget('cb_admin_privileges_roles');
    }

    public static function getPrivileges()
    {
        $roleId = cbUser()->cms_roles_id;

        return cache()->rememberForever('cb_admin_privileges_roles'. $roleId, function () use ($roleId) {
            return \DB::table('cms_roles_privileges')->where('cms_roles_id', $roleId)->join('cms_modules', 'cms_modules.id', '=', 'cms_roles_privileges.cms_modules_id')->select('cms_modules.name', 'cms_modules.path', 'can_see_module', 'can_create', 'can_read', 'can_edit', 'can_delete')->get() ?: [];
        });
    }
}