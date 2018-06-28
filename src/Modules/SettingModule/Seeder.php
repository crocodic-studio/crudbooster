<?php

namespace Crocodicstudio\Crudbooster\Modules\SettingModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    public static function run()
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

        SettingRepo::resetSettings($data);
    }
}