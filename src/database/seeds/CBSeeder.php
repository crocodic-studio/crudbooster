<?php

use Illuminate\Database\Seeder;

class CBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Please wait updating the data...');                        
        $this->call('PrivilegeSeeder');       
        $this->call('SettingsSeeder');
        $this->call('EmailTemplates');        
        $this->command->info('Updating the data completed !');
    }
}

class EmailTemplates extends Seeder {
    public function run() {
        DB::table('cms_email_templates')
        ->insert([                        
            'name'        =>'Email Template Forgot Password Backend',
            'slug'        =>'forgot_password_backend',
            'content'     =>'<p>Hi,</p><p>Someone requested forgot password, here is your new password : </p><p>[password]</p><p><br></p><p>--</p><p>Regards,</p><p>Admin</p>',
            'description' =>'[password]',
            'from_name'   =>'System',
            'from_email'  =>'system@crudbooster.com',
            'cc_email'    =>NULL            
            ]);
    }
}

class SettingsSeeder extends Seeder {

    public function run()
    {        

       $data = [

        //LOGIN REGISTER STYLE
        [   
            
            'name'=>'login_background_color',
            'label'=>'Login Background Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            
            'name'=>'login_font_color',
            'label'=>'Login Font Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            
            'name'=>'login_background_image',
            'label'=>'Login Background Image',
            'content'=>NULL,
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>NULL],


        //EMAIL SETTING
        [
            
            'name'=>'email_sender',
            'label'=>'Email Sender',            
            'content'=>'support@crudbooster.com',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'smtp_driver',
            'label'=>'Mail Driver',
            'content'=>'mail',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>'smtp,mail,sendmail',
            'helper'=>NULL],
        [
            
            'name'=>'smtp_host',
            'label'=>'SMTP Host',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'smtp_port',
            'label'=>'SMTP Port',
            'content'=>'25',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>'default 25'],
        [
            
            'name'=>'smtp_username',
            'label'=>'SMTP Username',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'smtp_password',
            'label'=>'SMTP Password',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],


        //APPLICATION SETTING
        [
            
            'name'=>'appname',
            'label'=>'Application Name',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'CRUDBooster',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'default_paper_size',
            'label'=>'Default Paper Print Size',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'Legal',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>'Paper size, ex : A4, Legal, etc'],        
        [
            
            'name'=>'logo',
            'label'=>'Logo',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'favicon',
            'label'=>'Favicon',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'api_debug_mode',
            'label'=>'API Debug Mode',
            'content'=>'true',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>'true,false',
            'helper'=>NULL],        
        [
            
            'name'=>'google_api_key',
            'label'=>'Google API Key',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            
            'name'=>'google_fcm_key',
            'label'=>'Google FCM Key',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL]
        ];

        foreach($data as $row) {
            $count = DB::table('cms_settings')->where('name',$row['name'])->count();
            if($count) {
                if($count > 1) {
                    $newsId = DB::table('cms_settings')->where('name',$row['name'])->orderby('id','asc')->take(1)->first();
                    DB::table('cms_settings')->where('name',$row['name'])->where('id','!=',$newsId->id)->delete();
                }                
                continue;
            }            
            DB::table('cms_settings')->insert($row);
        }
        
    }
}

class PrivilegeSeeder extends Seeder {

    public function run()
    {        
        
        if(DB::table('cms_privileges')->where('name','Super Administrator')->count() == 0) {
            $pid = DB::table('cms_privileges')->insertGetId([                                   
            'name'          =>'Super Administrator',
            'is_superadmin' =>1,
            'theme_color'   =>'skin-red'
			]);

            $password = \Hash::make('123456');
            $cms_users = DB::table('cms_users')->insert(array(                                
                'name'              => 'Super Admin',                
                'email'             => 'admin@crudbooster.com',
                'password'          => $password,
                'id_cms_privileges' => $pid,                
                'status'            =>'Active'
            ));
        }        
    }
}