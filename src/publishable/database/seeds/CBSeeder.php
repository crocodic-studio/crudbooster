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
        $this->call('CBSeederRun');        
        $this->command->info('Updating the data completed !');
    }
}

class CBSeederRun extends Seeder {
    public function run() {
        $this->seedEmailTemplates();
        $this->seedSettings();
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedModules();
        $this->seedUsers();
    }

    private function seedEmailTemplates() 
    {
        DB::table('cb_email_templates')
        ->insert([
            'id'          =>DB::table('cb_email_templates')->max('id')+1,
            'created_at'  =>date('Y-m-d H:i:s'),
            'name'        =>'Email Template Forgot Password Backend',
            'slug'        =>'forgot_password_backend',
            'content'     =>'<p>Hi,</p><p>Someone requested forgot password, here is your new password : </p><p>[password]</p><p><br></p><p>--</p><p>Regards,</p><p>Admin</p>',
            'description' =>'[password]',
            'from_name'   =>'CRUDBooster',
            'from_email'  =>'no-reply@crudbooster.com',
            'cc_email'    =>NULL            
            ]);
    }

    private function seedSettings() 
    {
        $data = [

        //LOGIN REGISTER STYLE
        [   
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_background_color',
            'label'=>'Login Background Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_font_color',
            'label'=>'Login Font Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_background_image',
            'label'=>'Login Background Image',
            'content'=>NULL,
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>NULL],


        //EMAIL SETTING
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'email_sender',
            'label'=>'Email Sender',            
            'content'=>'support@crudbooster.com',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_driver',
            'label'=>'Mail Driver',
            'content'=>'mail',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>'smtp,mail,sendmail',
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_host',
            'label'=>'SMTP Host',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_port',
            'label'=>'SMTP Port',
            'content'=>'25',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>'default 25'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_username',
            'label'=>'SMTP Username',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_password',
            'label'=>'SMTP Password',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],


        //APPLICATION SETTING
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'appname',
            'label'=>'Application Name',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'CRUDBooster',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'default_paper_size',
            'label'=>'Default Paper Print Size',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'Legal',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>'Paper size, ex : A4, Legal, etc'],        
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'logo',
            'label'=>'Logo',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'favicon',
            'label'=>'Favicon',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'api_debug_mode',
            'label'=>'API Debug Mode',
            'content'=>'true',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>'true,false',
            'helper'=>NULL],        
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'google_api_key',
            'label'=>'Google API Key',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
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
            $row['id'] = DB::table('cms_settings')->max('id') + 1;
            DB::table('cms_settings')->insert($row);
        }
    } 

    private function seedPermissions() 
    {
        if(DB::table('cb_permissions')->count() == 0) {
            $modules = DB::table('cb_modules')->get();
            $i = 1;
            foreach($modules as $module) {

                $canVisible = 1;
                $canCreate  = 1;
                $canRead    = 1;
                $canEdit    = 1;
                $canDelete  = 1;

                switch($module->table) {
                    case 'cb_logs':
                        $canCreate = 0;
                        $canEdit   = 0;
                    break;
                    case 'cb_permissions':
                        $canVisible = 0;
                    break;
                    case 'cb_api':
                        $canVisible = 0;
                    break;
                    case 'cb_notifications':
                        $canCreate = $canRead = $canEdit = $canDelete = 0;
                    break;
                }

                DB::table('cb_permissions')->insert([
                    'id'=>DB::table('cb_permissions')->max('id')+1,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'can_visible'=>$canVisible,
                    'can_create'=>$canCreate,
                    'can_edit'=>$canEdit,
                    'can_delete'=>$canDelete,
                    'can_read'=>$canRead,
                    'cb_roles'=>1,
                    'cb_modules'=>$module->id
                ]);
                $i++;
            }
        }
    }

    private function seedRoles() 
    {
        if(DB::table('cb_roles')->where('name','Super Administrator')->count() == 0) {
            DB::table('cb_roles')->insert([    
            'id'            =>DB::table('cb_roles')->max('id')+1,        
            'created_at'    =>date('Y-m-d H:i:s'),
            'name'          =>'Super Administrator',
            'is_superadmin' =>1,
            'theme_color'   =>'skin-red'
            ]);    
        }
    }

    private function seedModules()
    {        
        $data = [
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Notifications',
            'icon'=>'fa fa-cog',
            'path'=>'notifications',
            'table'=>'cb_notifications',
            'controller'=>'CBNotificationsController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Roles',
            'icon'=>'fa fa-cog',
            'path'=>'roles',
            'table'=>'cb_roles',
            'controller'=>'CBRolesController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Permissions',
            'icon'=>'fa fa-cog',
            'path'=>'permissions',
            'table'=>'cb_permissions',
            'controller'=>'CBPermissionsController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Users',
            'icon'=>'fa fa-users',
            'path'=>'users',
            'table'=>'users',
            'controller'=>'CBUsersController',     
            'is_protected'=>0,                                   
            'is_active'=>1
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Settings',
            'icon'=>'fa fa-cog',
            'path'=>'settings',
            'table'=>'cb_settings',
            'controller'=>'CBSettingsController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Modules',
            'icon'=>'fa fa-database',
            'path'=>'modules',
            'table'=>'cb_modules',
            'controller'=>'CBModulesController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Menu Management',
            'icon'=>'fa fa-bars',
            'path'=>'menus',
            'table'=>'cb_menus',
            'controller'=>'CBMenusController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Email Templates',
            'icon'=>'fa fa-envelope-o',
            'path'=>'email_templates',
            'table'=>'cb_email_templates',
            'controller'=>'CBEmailTemplatesController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Statistic Builder',
            'icon'=>'fa fa-dashboard',
            'path'=>'statistic_builder',
            'table'=>'cb_statistics',
            'controller'=>'CBStatisticBuilderController',            
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'API Management',
            'icon'=>'fa fa-cloud-download',
            'path'=>'api',
            'table'=>'',
            'controller'=>'CBApiController',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Logs',
            'icon'=>'fa fa-flag-o',
            'path'=>'logs',
            'table'=>'cb_logs',
            'controller'=>'CBLogsController',
            'is_protected'=>1,                                
            'is_active'=>1
        ]      
            ];


        foreach($data as $k=>$d) {
            if(DB::table('cb_modules')->where('name',$d['name'])->count()) {
                unset($data[$k]);
            }
        }

        DB::table('cb_modules')->insert($data);
    }

    private function seedUsers()
    {                
        if(DB::table('users')->count() == 0) {
            $password = \Hash::make('123456');
            $users = DB::table('users')->insert(array(
                'id'                =>DB::table('users')->max('id')+1,
                'created_at'        =>date('Y-m-d H:i:s'),
                'name'              => 'Super Admin',                
                'email'             => 'admin@crudbooster.com',
                'password'          => $password,
                'cb_roles_id'       => 1,                
                'status'            =>'Active'
            ));
        }
    }
}