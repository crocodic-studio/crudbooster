<?php

namespace crocodicstudio\crudbooster\Modules\EmailTemplates;

use Illuminate\Support\Facades\DB;

class Seeder
{
    public static function run()
    {
        DB::table('cms_email_templates')->insert([
            'name' => 'Email Template Forgot Password Backend',
            'slug' => 'forgot_password_backend',
            'content' => '<p>Hi,</p><p>Someone requested forgot password, here is your new password : </p><p>[password]</p><p><br></p><p>--</p><p>Regards,</p><p>Admin</p>',
            'description' => '[password]',
            'from_name' => 'System',
            'from_email' => 'system@crudbooster.com',
            'cc_email' => null,
        ]);
    }
}