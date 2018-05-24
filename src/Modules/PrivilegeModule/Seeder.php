<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
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

        app(CbUsersRepo::class)->insert([
            'name' => 'Super Admin',
            'email' => 'admin@crudbooster.com',
            'password' => bcrypt('123456'),
            'id_cms_privileges' => $pid,
            'status' => 'Active',
        ]);
    }
}