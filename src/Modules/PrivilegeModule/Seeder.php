<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;

class Seeder
{
    public static function run()
    {
        $count = DB::table('cms_privileges')->where('name', 'Super Administrator')->count();
        if ($count != 0) {
            return;
        }
        unset($count);
        $pid = DB::table('cms_privileges')->insertGetId([
            'name' => 'Super Administrator',
            'is_superadmin' => 1,
            'theme_color' => 'skin-red',
        ]);

        $password = \Hash::make('123456');
        $cms_users = CbUsersRepo::table()->insert([
            'name' => 'Super Admin',
            'email' => 'admin@crudbooster.com',
            'password' => $password,
            'id_cms_privileges' => $pid,
            'status' => 'Active',
        ]);
    }
}