<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

use Illuminate\Support\Facades\DB;

class Seeder
{
    public static function seed()
    {

        $data = [
            //LOGIN REGISTER STYLE
            [

                'name' => 'login_background_color',
                'label' => 'Login Background Color',
                'content' => null,
                'content_input_type' => 'text',
                'group_setting' => cbTrans('login_register_style'),
                'dataenum' => null,
                'helper' => 'Input hexacode',
            ],
            [

                'name' => 'login_font_color',
                'label' => 'Login Font Color',
                'content' => null,
                'content_input_type' => 'text',
                'group_setting' => cbTrans('login_register_style'),
                'dataenum' => null,
                'helper' => 'Input hexacode',
            ],
            [

                'name' => 'login_background_image',
                'label' => 'Login Background Image',
                'content' => null,
                'content_input_type' => 'upload_image',
                'group_setting' => cbTrans('login_register_style'),
                'dataenum' => null,
                'helper' => null,
            ],

            //EMAIL SETTING
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

            //APPLICATION SETTING
            [

                'name' => 'appname',
                'label' => 'Application Name',
                'group_setting' => cbTrans('application_setting'),
                'content' => 'CRUDBooster',
                'content_input_type' => 'text',
                'dataenum' => null,
                'helper' => null,
            ],
            [

                'name' => 'default_paper_size',
                'label' => 'Default Paper Print Size',
                'group_setting' => cbTrans('application_setting'),
                'content' => 'Legal',
                'content_input_type' => 'text',
                'dataenum' => null,
                'helper' => 'Paper size, ex : A4, Legal, etc',
            ],
            [

                'name' => 'logo',
                'label' => 'Logo',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => cbTrans('application_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [

                'name' => 'favicon',
                'label' => 'Favicon',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => cbTrans('application_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'name' => 'api_debug_mode',
                'label' => 'API Debug Mode',
                'content' => 'true',
                'content_input_type' => 'select',
                'group_setting' => cbTrans('application_setting'),
                'dataenum' => 'true,false',
                'helper' => null,
            ],
            [

                'name' => 'google_api_key',
                'label' => 'Google API Key',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('application_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [

                'name' => 'google_fcm_key',
                'label' => 'Google FCM Key',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => cbTrans('application_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
        ];

        foreach ($data as $row) {
            $count = DB::table('cms_settings')->where('name', $row['name'])->count();
            if ($count) {
                if ($count > 1) {
                    $newsId = DB::table('cms_settings')->where('name', $row['name'])->orderby('id', 'asc')->take(1)->first();
                    DB::table('cms_settings')->where('name', $row['name'])->where('id', '!=', $newsId->id)->delete();
                }
                continue;
            }
            DB::table('cms_settings')->insert($row);
        }
    }
}