<?php

namespace Crocodicstudio\Crudbooster\Modules\EmailTemplates;

use Crocodicstudio\Crudbooster\Modules\SettingModule\SettingRepo;
use Illuminate\Database\Seeder as BaseSeeder;
use Illuminate\Support\Facades\DB;

class Seeder extends BaseSeeder
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
        $data = [
            [
                'name' => 'email_sender',
                'label' => 'Email Sender',
                'content' => 'support@crudbooster.com',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'name' => 'smtp_driver',
                'label' => 'Mail Driver',
                'content' => 'mail',
                'content_input_type' => 'select',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => 'smtp,mail,sendmail',
                'helper' => null,
            ],
            [
                'name' => 'smtp_host',
                'label' => 'SMTP Host',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'name' => 'smtp_port',
                'label' => 'SMTP Port',
                'content' => '25',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => null,
                'helper' => 'default 25',
            ],
            [
                'name' => 'smtp_username',
                'label' => 'SMTP Username',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'name' => 'smtp_password',
                'label' => 'SMTP Password',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],

        ];

        SettingRepo::resetSettings($data);
    }
}