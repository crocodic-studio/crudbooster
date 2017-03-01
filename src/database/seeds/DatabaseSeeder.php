<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Please wait updating the data...');        
        
        $this->call('Cms_usersSeeder');                           
        $this->call('Cms_modulsSeeder');                         
        $this->call('Cms_privilegesSeeder');        
        $this->call('Cms_privileges_rolesSeeder');       
        $this->call('Cms_settingsSeeder');
        $this->call('CmsEmailTemplates');
        
        $this->command->info('Updating the data completed !');
    }
}

class CmsEmailTemplates extends Seeder {
    public function run() {
        DB::table('cms_email_templates')
        ->insert([
            'id'          =>DB::table('cms_email_templates')->max('id')+1,
            'created_at'  =>date('Y-m-d H:i:s'),
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

class Cms_settingsSeeder extends Seeder {

    public function run()
    {        

       $data = [

        //LOGIN REGISTER STYLE
        [   
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_background_color',
            'label'=>'Login Background Color',
            'translate_group_setting'=>'{"ar":{"singular":"Login Register Style","plural":"Login Register Style"},"en":{"singular":"Login Register Style","plural":"Login Register Style"},"pt_br":{"singular":"Estilo para Registro","plural":"Estilo para Registro"}}',
            'translate_label'=>'{"ar":{"singular":"Login Background Color","plural":"Login Background Color"},"en":{"singular":"Login Background Color","plural":"Login Background Color"},"pt_br":{"singular":"Cor de Fundo para Login","plural":"Cor de Fundo para Login"}}',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_font_color',
            'label'=>'Login Font Color',
            'translate_group_setting'=>'{"ar":{"singular":"Login Register Style","plural":"Login Register Style"},"en":{"singular":"Login Register Style","plural":"Login Register Style"},"pt_br":{"singular":"Estilo para Registro","plural":"Estilo para Registro"}}',
            'translate_label'=>'{"ar":{"singular":"Login Font Color","plural":"Login Font Color"},"en":{"singular":"Login Font Color","plural":"Login Font Color"},"pt_br":{"singular":"Cor da Fonte para Login","plural":"Cor da Fonte para Login"}}',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.login_register_style'),
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'login_background_image',
            'label'=>'Login Background Image',
            'translate_group_setting'=>'{"ar":{"singular":"Login Register Style","plural":"Login Register Style"},"en":{"singular":"Login Register Style","plural":"Login Register Style"},"pt_br":{"singular":"Estilo para Registro","plural":"Estilo para Registro"}}',
            'translate_label'=>'{"ar":{"singular":"Login Background Image","plural":"Login Background Image"},"en":{"singular":"Login Background Image","plural":"Login Background Image"},"pt_br":{"singular":"Imagem de Fundo para Login","plural":"Imagem de Fundo para Login"}}',
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
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"Email Sender","plural":"Email Sender"},"en":{"singular":"Email Sender","plural":"Email Sender"},"pt_br":{"singular":"Remetente","plural":"Remetente"}}',
            'content'=>'support@crudbooster.com',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_driver',
            'label'=>'Mail Driver',
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"Mail Driver","plural":"Mail Driver"},"en":{"singular":"Mail Driver","plural":"Mail Driver"},"pt_br":{"singular":"Drive de Email","plural":"Drive de Email"}}',
            'content'=>'mail',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>'smtp,mail,sendmail',
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_host',
            'label'=>'SMTP Host',
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"SMTP Host","plural":"SMTP Host"},"en":{"singular":"SMTP Host","plural":"SMTP Host"},"pt_br":{"singular":"Servidor de SMTP","plural":"Servidor de SMTP"}}',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_port',
            'label'=>'SMTP Port',
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"SMTP Port","plural":"SMTP Port"},"en":{"singular":"SMTP Port","plural":"SMTP Port"},"pt_br":{"singular":"Porta do servidor de SMTP","plural":"Porta do servidor de SMTP"}}',
            'content'=>'25',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>'default 25'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_username',
            'label'=>'SMTP Username',
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"SMTP Username","plural":"SMTP Username"},"en":{"singular":"SMTP Username","plural":"SMTP Username"},"pt_br":{"singular":"Usuário do servidor de SMTP","plural":"Usuário do servidor de SMTP"}}',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.email_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_password',
            'label'=>'SMTP Password',
            'translate_group_setting'=>'{"ar":{"singular":"Email Setting","plural":"Email Setting"},"en":{"singular":"Email Setting","plural":"Email Setting"},"pt_br":{"singular":"Configurações para Email","plural":"Configurações para Email"}}',
            'translate_label'=>'{"ar":{"singular":"SMTP Password","plural":"SMTP Password"},"en":{"singular":"SMTP Password","plural":"SMTP Password"},"pt_br":{"singular":"Senha do usuário SMTP","plural":"Senha do usuário SMTP"}}',
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
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Application Name","plural":"Application Name"},"en":{"singular":"Application Name","plural":"Application Name"},"pt_br":{"singular":"Nome da Aplicação","plural":"Nome da Aplicação"}}',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'CRUDBooster',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'default_paper_size',
            'label'=>'Default Paper Print Size',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Default Paper Print Size","plural":"Default Paper Print Size"},"en":{"singular":"Default Paper Print Size","plural":"Default Paper Print Size"},"pt_br":{"singular":"Tamanho Padrão da Página de Impressão","plural":"Tamanho Padrão da Página de Impressão"}}',
            'group_setting'=>trans('crudbooster.application_setting'),
            'content'=>'Legal',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>'Paper size, ex : A4, Legal, etc'],        
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'logo',
            'label'=>'Logo',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Logo","plural":"Logo"},"en":{"singular":"Logo","plural":"Logo"},"pt_br":{"singular":"Logo","plural":"Logo"}}',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'favicon',
            'label'=>'Favicon',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Favicon","plural":"Favicon"},"en":{"singular":"Favicon","plural":"Favicon"},"pt_br":{"singular":"Favicon","plural":"Favicon"}}',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'api_debug_mode',
            'label'=>'API Debug Mode',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"API Debug Mode","plural":"API Debug Mode"},"en":{"singular":"API Debug Mode","plural":"API Debug Mode"},"pt_br":{"singular":"Modo de Depuração API","plural":"Modo de Depuração API"}}',
            'content'=>'true',
            'content_input_type'=>'select',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>'true,false',
            'helper'=>NULL],        
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'google_api_key',
            'label'=>'Google API Key',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Google API Key","plural":"Google API Key"},"en":{"singular":"Google API Key","plural":"Google API Key"},"pt_br":{"singular":"Google API Key","plural":"Google API Key"}}',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'google_fcm_key',
            'label'=>'Google FCM Key',
            'translate_group_setting'=>'{"ar":{"singular":"Application Setting","plural":"Application Setting"},"en":{"singular":"Application Setting","plural":"Application Setting"},"pt_br":{"singular":"Configuração da Aplicação","plural":"Configurações da Aplicação"}}',
            'translate_label'=>'{"ar":{"singular":"Google FCM Key","plural":"Google FCM Key"},"en":{"singular":"Google FCM Key","plural":"Google FCM Key"},"pt_br":{"singular":"Google FCM Key","plural":"Google FCM Key"}}',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>trans('crudbooster.application_setting'),
            'dataenum'=>NULL,
            'helper'=>NULL]
        ];

        foreach($data as $row) {
            if(DB::table('cms_settings')->where('name',$d['name'])->count()) {
                continue;
            }
            $row['id'] = DB::table('cms_settings')->max('id') + 1;
            DB::table('cms_settings')->insert($row);
        }
        
    }
}



class Cms_privileges_rolesSeeder extends Seeder {

    public function run()
    {                

        if(DB::table('cms_privileges_roles')->count() == 0) {
            $modules = DB::table('cms_moduls')->get();
            $i = 1;
            foreach($modules as $module) {

                $is_visible = 1;
                $is_create  = 1;
                $is_read    = 1;
                $is_edit    = 1;
                $is_delete  = 1;

                switch($module->table_name) {
                    case 'cms_logs':
                        $is_create = 0;
                        $is_edit   = 0;
                    break;
                    case 'cms_privileges_roles':
                        $is_visible = 0;
                    break;
                    case 'cms_apicustom':
                        $is_visible = 0;
                    break;
                    case 'cms_notifications':
                        $is_create = $is_read = $is_edit = $is_delete = 0;
                    break;
                }

                DB::table('cms_privileges_roles')->insert([
                    'id'=>DB::table('cms_privileges_roles')->max('id')+1,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'is_visible'=>$is_visible,
                    'is_create'=>$is_create,
                    'is_edit'=>$is_edit,
                    'is_delete'=>$is_delete,
                    'is_read'=>$is_read,
                    'id_cms_privileges'=>1,
                    'id_cms_moduls'=>$module->id
                    ]);
                $i++;
            }
        }
        
    }
}

class Cms_privilegesSeeder extends Seeder {

    public function run()
    {        
        
        if(DB::table('cms_privileges')->where('name','Super Administrator')->count() == 0) {
            DB::table('cms_privileges')->insert([    
            'id'            =>DB::table('cms_privileges_roles')->max('id')+1,        
            'created_at'    =>date('Y-m-d H:i:s'),
            'name'          =>'Super Administrator',
            'is_superadmin' =>1,
            'theme_color'   =>'skin-red'
            ]);    
        }        
    }
}

class Cms_modulsSeeder extends Seeder {

    public function run()
    {        

        /* 
            1 = Public
            2 = Setting        
        */

        $data = [
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Notifications',
            'icon'=>'fa fa-cog',
            'path'=>'notifications',
            'table_name'=>'cms_notifications',
            'controller'=>'NotificationsController',
            'translate'=>'{"en":{"singular":"Notification","plural":"Notifications"},"pt_br":{"singular":"Notificação","plural":"Notificações"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Privileges',
            'icon'=>'fa fa-cog',
            'path'=>'privileges',
            'table_name'=>'cms_privileges',
            'controller'=>'PrivilegesController',
            'translate'=>'{"en":{"singular":"Privilege","plural":"Privileges"},"pt_br":{"singular":"Privilégio","plural":"Privilégios"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Privileges Roles',
            'icon'=>'fa fa-cog',
            'path'=>'privileges_roles',
            'table_name'=>'cms_privileges_roles',
            'controller'=>'PrivilegesRolesController',
            'translate'=>'{"en":{"singular":"Privilege Role","plural":"Privileges Roles"},"pt_br":{"singular":"Regra de Privilégio","plural":"Regras de Privilégios"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Users',
            'icon'=>'fa fa-users',
            'path'=>'users',
            'table_name'=>'cms_users',
            'controller'=>'AdminCmsUsersController',     
            'translate'=>'{"en":{"singular":"User","plural":"Users"},"pt_br":{"singular":"Usuário","plural":"Usuários"}}',
            'is_protected'=>0,                                   
            'is_active'=>1
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Settings',
            'icon'=>'fa fa-cog',
            'path'=>'settings',
            'table_name'=>'cms_settings',
            'controller'=>'SettingsController',
            'translate'=>'{"en":{"singular":"Setting","plural":"Settings"},"pt_br":{"singular":"Configuração","plural":"Configurações"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Module Generator',
            'icon'=>'fa fa-database',
            'path'=>'module_generator',
            'table_name'=>'cms_moduls',
            'controller'=>'ModulsController',
            'translate'=>'{"en":{"singular":"Module Generator","plural":"Module Generator"},"pt_br":{"singular":"Gerador de Módulo","plural":"Gerador de Módulos"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Menu Management',
            'icon'=>'fa fa-bars',
            'path'=>'menu_management',
            'table_name'=>'cms_menus',
            'controller'=>'MenusController',
            'translate'=>'{"en":{"singular":"Menu Management","plural":"Menu Management"},"pt_br":{"singular":"Gerenciador de Menu","plural":"Gerenciador de Menus"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Email Template',
            'icon'=>'fa fa-envelope-o',
            'path'=>'email_templates',
            'table_name'=>'cms_email_templates',
            'controller'=>'EmailTemplatesController',
            'translate'=>'{"en":{"singular":"Email Template","plural":"Email Template"},"pt_br":{"singular":"Modelo de Email","plural":"Modelos de Emails"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Statistic Builder',
            'icon'=>'fa fa-dashboard',
            'path'=>'statistic_builder',
            'table_name'=>'cms_statistics',
            'controller'=>'StatisticBuilderController',            
            'translate'=>'{"en":{"singular":"Statistic Builder","plural":"Statistic Builder"},"pt_br":{"singular":"Gerador de Estatística","plural":"Gerador de Estatísticas"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'API Generator',
            'icon'=>'fa fa-cloud-download',
            'path'=>'api_generator',
            'table_name'=>'',
            'controller'=>'ApiCustomController',
            'translate'=>'{"en":{"singular":"API Generator","plural":"API Generator"},"pt_br":{"singular":"Gerador de API","plural":"Gerador de API"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Logs',
            'icon'=>'fa fa-flag-o',
            'path'=>'logs',
            'table_name'=>'cms_logs',
            'controller'=>'LogsController',
            'translate'=>'{"en":{"singular":"Log","plural":"Logs"},"pt_br":{"singular":"Log","plural":"Logs"}}',
            'is_protected'=>1,                                
            'is_active'=>1
        ]      
            ];


        foreach($data as $k=>$d) {
            if(DB::table('cms_moduls')->where('name',$d['name'])->count()) {
                unset($data[$k]);
            }
        }

        DB::table('cms_moduls')->insert($data);
    }

}

class Cms_usersSeeder extends Seeder {

    public function run()
    {        
        
        if(DB::table('cms_users')->count() == 0) {
            $password = \Hash::make('123456');
            $cms_users = DB::table('cms_users')->insert(array(
                'id'                =>DB::table('cms_users')->max('id')+1,
                'created_at'        =>date('Y-m-d H:i:s'),
                'name'              => 'Super Admin',                
                'email'             => 'admin@crudbooster.com',
                'password'          => $password,
                'id_cms_privileges' => 1,                
                'status'            =>'Active'
            ));
        }            

    }

}

