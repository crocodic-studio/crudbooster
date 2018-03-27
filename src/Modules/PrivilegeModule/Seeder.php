<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use Illuminate\Support\Facades\DB;

class Seeder
{
    public static function run()
    {
        $exists = DB::table('cms_privileges')->where('name', 'Super Administrator')->exists();
        if ($exists) {
            return;
        }
        unset($exists);
        $pid = DB::table('cms_privileges')->insertGetId([
            'name' => 'Super Administrator',
            'is_superadmin' => 1,
            'theme_color' => 'skin-red',
        ]);

        CbUsersRepo::table()->insert([
            'name' => 'Super Admin',
            'email' => 'admin@crudbooster.com',
            'password' => \Hash::make('123456'),
            'id_cms_privileges' => $pid,
            'status' => 'Active',
        ]);
    }
}