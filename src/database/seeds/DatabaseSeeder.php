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
        $this->call('Cms_companiesSeeder');        
        $this->call('Cms_dashboardSeeder');        
        $this->call('Cms_modulsSeeder');        
        $this->call('Cms_moduls_groupSeeder');              
        $this->call('Cms_privilegesSeeder');        
        $this->call('Cms_privileges_rolesSeeder');       
        $this->call('Cms_settingsSeeder');              
        $this->call('Cms_postscategoriesSeeder');              
        $this->call('Cms_postsSeeder');      
        $this->call('ApiUpdateVersion');      
        
        $this->command->info('Updating the data completed !');
    }
}

class ApiUpdateVersion extends Seeder {
    public function run() {
        $apis = DB::table('cms_apicustom')->whereraw('kolom IS NOT NULL OR sub_query_1 IS NOT NULL')->get();
        foreach($apis as $a) {
            @$subquery  = explode(',',$a->sub_query_1);
            @$kolom     = explode(',',$a->kolom);
            @$parameter = explode(',',$a->parameter);

            @$subquery  = array_filter($subquery);
            @$kolom     = array_filter($kolom);
            @$parameter = array_filter($parameter);

            $params     = array();
            $respons    = array();

            if($a->aksi == 'list' || $a->aksi == 'detail') {
                if($parameter) {
                    foreach($parameter as $p) {         
                        if($p) {
                            $params[] = array('name'=>$p,'type'=>'string','config'=>'','required'=>1,'used'=>1);
                        }                                       
                    }
                }
                
                if($kolom) {
                    foreach($kolom as $k) {
                        if($k) {
                            $respons[] = array('name'=>$k,'type'=>'string','subquery'=>'','used'=>1);
                        }                        
                    }
                }

                if($subquery) {
                    foreach($subquery as $s) {
                        $s = trim($s);
                        $s = strtolower($s);
                        $sraw = explode(') as ',$s);
                        $config = substr($sraw[0],1);
                        $name = $sraw[1];
                        if($name) {
                            $respons[] = array('name'=>$name,'type'=>'string','subquery'=>$config,'used'=>1);
                        }
                    }
                }
            }elseif ($a->aksi == 'save_add') {
                if($kolom) {
                    foreach($kolom as $k) {
                        if($k) {
                            $params[] = array('name'=>$k,'type'=>'string','config'=>'','used'=>1,'required'=>1);
                        }                        
                    }
                }
            }elseif ($a->aksi == 'save_edit') {
                if($kolom) {
                    foreach($kolom as $k) {
                        if($k) {
                            $params[] = array('name'=>$k,'type'=>'string','config'=>'','used'=>1,'required'=>1);
                        }                        
                    }
                }
                if($parameter) {
                    foreach($parameter as $p) {   
                        if($p) {
                            $params[] = array('name'=>$p,'type'=>'string','config'=>'','required'=>1,'used'=>1);
                        }                                             
                    }
                }
            }elseif ($a->aksi == 'delete') {
                if($parameter) {
                    foreach($parameter as $p) {  
                        if($p) {
                            $params[] = array('name'=>$p,'type'=>'string','config'=>'','required'=>1,'used'=>1);
                        }                                              
                    }
                }
            }


            DB::table('cms_apicustom')
            ->where('id',$a->id)
            ->update([
                'kolom'=>NULL,
                'sub_query_1'=>NULL,
                'parameter'=>NULL,
                'parameters'=>serialize($params),
                'responses'=>serialize($respons)
                ]);
        }                
    }
}

class Cms_postsSeeder extends Seeder {
    public function run() {

        if(DB::table('cms_posts')->count() == 0) {
            $faker = Faker\Factory::create();
            $id_cms_users = DB::table('cms_users')->lists('id');
            $id_cms_posts_categories = DB::table('cms_posts_categories')->lists('id');

            for($i=1;$i<=30;$i++) {
                $a = array();
                $a['created_at'] = $faker->dateTimeBetween('-1 months','now');
                $a['title'] = $faker->sentence;
                $a['content'] = $faker->paragraph;
                $a['id_cms_users'] = $faker->randomElement($id_cms_users);
                $a['id_cms_posts_categories'] = $faker->randomElement($id_cms_posts_categories);
                DB::table('cms_posts')->insert($a);
            }
        }

        
    }
}

class Cms_postscategoriesSeeder extends Seeder {
    public function run() {
        if(DB::table('cms_posts_categories')->count() == 0) {
            $faker = Faker\Factory::create();
            for($i=1;$i<=5;$i++) {
                $a               = array();
                $a['created_at'] = $faker->dateTimeBetween('-1 months','now');
                $a['name']       = $faker->words(3,true);
                DB::table('cms_posts_categories')->insert($a);
            }
        }        
    }
}


class Cms_settingsSeeder extends Seeder {

    public function run()
    {        

       $data = [

        //LOGIN REGISTER STYLE
        [   
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'logo_background_color',
            'label'=>'Logo Background Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>'Login Register Style',
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'logo_font_color',
            'label'=>'Logo Font Color',
            'content'=>NULL,
            'content_input_type'=>'text',
            'group_setting'=>'Login Register Style',
            'dataenum'=>NULL,
            'helper'=>'Input hexacode'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'logo_background_image',
            'label'=>'Logo Background Image',
            'content'=>NULL,
            'content_input_type'=>'upload_image',
            'group_setting'=>'Login Register Style',
            'dataenum'=>NULL,
            'helper'=>NULL],


        //EMAIL SETTING
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'email_sender',
            'label'=>'Email Sender',            
            'content'=>'support@crudbooster.com',
            'content_input_type'=>'text',
            'group_setting'=>'Email Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_driver',
            'label'=>'Mail Driver',
            'content'=>'mail',
            'content_input_type'=>'select',
            'group_setting'=>'Email Setting',
            'dataenum'=>'smtp,mail,sendmail',
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_host',
            'label'=>'SMTP Host',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>'Email Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_port',
            'label'=>'SMTP Port',
            'content'=>'25',
            'content_input_type'=>'text',
            'group_setting'=>'Email Setting',
            'dataenum'=>NULL,
            'helper'=>'default 25'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_username',
            'label'=>'SMTP Username',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>'Email Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'smtp_password',
            'label'=>'SMTP Password',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>'Email Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],


        //APPLICATION SETTING
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'appname',
            'label'=>'Application Name',
            'group_setting'=>'Application Setting',
            'content'=>'CRUDBooster',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'app_lockscreen_timeout',
            'label'=>'Lockscreen Timeout',
            'group_setting'=>'Application Setting',
            'content'=>'60',
            'content_input_type'=>'text',
            'dataenum'=>NULL,
            'helper'=>'in minutes'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'default_paper_size',
            'label'=>'Default Paper Print Size',
            'group_setting'=>'Application Setting',
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
            'group_setting'=>'Application Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'favicon',
            'label'=>'Favicon',
            'content'=>'',
            'content_input_type'=>'upload_image',
            'group_setting'=>'Application Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'api_debug_mode',
            'label'=>'API Debug Mode',
            'content'=>'true',
            'content_input_type'=>'select',
            'group_setting'=>'Application Setting',
            'dataenum'=>'true,false',
            'helper'=>NULL],        
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'google_api_key',
            'label'=>'Google API Key',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>'Application Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'google_fcm_key',
            'label'=>'Google FCM Key',
            'content'=>'',
            'content_input_type'=>'text',
            'group_setting'=>'Application Setting',
            'dataenum'=>NULL,
            'helper'=>NULL],

        //EMAIL TEMPLATE
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'register_email_confirmation',
            'label'=>'Registration Email Confirmation',
            'group_setting'=>'Email Template',
            'content'=>'',
            'content_input_type'=>'wysiwyg',
            'dataenum'=>NULL,
            'helper'=>'Input message about email confirmation here, use alias [link_confirmation] to include the link.'],
        [
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'register_email_welcome',
            'group_setting'=>'Email Template',
            'label'=>'Registration Email Welcome',
            'content'=>'',
            'content_input_type'=>'wysiwyg',
            'dataenum'=>NULL,
            'helper'=>"You can input message after registration is success. Then the message will be send to registrant. use [name] as user 's name, use [email] as user's email"]
        ];

        foreach($data as $k=>$d) {
            if(DB::table('cms_settings')->where('name',$d['name'])->count()) {
                unset($data[$k]);
            }
        }

        DB::table('cms_settings')->insert($data);
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
                    // 'id'=>$i,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'is_visible'=>$is_visible,
                    'is_create'=>$is_create,
                    'is_edit'=>$is_edit,
                    'is_delete'=>$is_delete,
                    'is_read'=>$is_read,
                    'id_cms_privileges'=>1,
                    'id_cms_moduls'=>$module->id
                    ]);

                if($module->table_name == 'cms_posts') {
                    $i++;
                    DB::table('cms_privileges_roles')->insert([
                    // 'id'=>$i,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'is_visible'=>$is_visible,
                    'is_create'=>$is_create,
                    'is_edit'=>$is_edit,
                    'is_delete'=>$is_delete,
                    'is_read'=>$is_read,
                    'id_cms_privileges'=>2,
                    'id_cms_moduls'=>$module->id
                    ]);
                }
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
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Super Administrator',
            'is_superadmin'=>1,
            'theme_color'=>'skin-red'
            ]);    
        }
        
        if(DB::table('cms_privileges')->count() == 0) {
            DB::table('cms_privileges')->insert([            
                'created_at'=>date('Y-m-d H:i:s'),
                'name'=>'Member',
                'is_superadmin'=>0,
                'is_register'=>1,
                'theme_color'=>'skin-green-light'
                ]);
        }
    }
}

class Cms_modulsSeeder extends Seeder {

    public function run()
    {        

        /* 
            1 = Public
            2 = Articles
            3 = Users
            4 = Setting         
        */

        $data = [
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Notifications',
            'icon'=>'fa fa-cog',
            'path'=>'notifications',
            'table_name'=>'cms_notifications',
            'controller'=>'NotificationsController',
            'sql_where'=>'',
            'sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Privileges',
            'icon'=>'fa fa-cog',
            'path'=>'privileges',
            'table_name'=>'cms_privileges',
            'controller'=>'PrivilegesController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>6,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>3,
            'is_softdelete'=>0,
            'is_active'=>1
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Privileges Roles',
            'icon'=>'fa fa-cog',
            'path'=>'privileges_roles',
            'table_name'=>'cms_privileges_roles',
            'controller'=>'PrivilegesRolesController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>8,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>0
        ],
        [
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Moduls',
            'icon'=>'fa fa-cog',
            'path'=>'cms_moduls',
            'table_name'=>'cms_moduls',
            'controller'=>'ModulsController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>2,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>0
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Users',
            'icon'=>'fa fa-users',
            'path'=>'users',
            'table_name'=>'cms_users',
            'controller'=>'UsersController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>3,
            'is_softdelete'=>0,
            'is_active'=>1
        ],
        [   
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Settings',
            'icon'=>'fa fa-cog',
            'path'=>'settings',
            'table_name'=>'cms_settings',
            'controller'=>'SettingsController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Module Generator',
            'icon'=>'fa fa-database',
            'path'=>'module_generator',
            'table_name'=>'cms_moduls_group',
            'controller'=>'ModulsGroupController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'API Generator',
            'icon'=>'fa fa-cloud-download',
            'path'=>'api_generator',
            'table_name'=>'',
            'controller'=>'ApiCustomController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Companies',
            'icon'=>'fa fa-bank',
            'path'=>'companies',
            'table_name'=>'cms_companies',
            'controller'=>'CompaniesController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>1,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Logs',
            'icon'=>'fa fa-flag-o',
            'path'=>'logs',
            'table_name'=>'cms_logs',
            'controller'=>'LogsController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Menu Group',
            'icon'=>'fa fa-flag-o',
            'path'=>'cms_menus_groups',
            'table_name'=>'cms_menus_groups',
            'controller'=>'MenusGroupsController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Menu',
            'icon'=>'fa fa-bars',
            'path'=>'cms_menus',
            'table_name'=>'cms_menus',
            'controller'=>'MenusController',
            'sql_where'=>'',
            'sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>4,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Articles',
            'icon'=>'fa fa-bars',
            'path'=>'cms_posts',
            'table_name'=>'cms_posts',
            'controller'=>'PostsController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>2,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Categories',
            'icon'=>'fa fa-bars',
            'path'=>'cms_posts_categories',
            'table_name'=>'cms_posts_categories',
            'controller'=>'PostsCategoriesController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>2,
            'is_softdelete'=>0,
            'is_active'=>1
        ],[ 
            
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Pages',
            'icon'=>'fa fa-bars',
            'path'=>'cms_pages',
            'table_name'=>'cms_pages',
            'controller'=>'PagesController',
            'sql_where'=>'','sql_orderby'=>'',
            'sorting'=>1,
            'limit_data'=>NULL,
            'id_cms_moduls_group'=>1,
            'is_softdelete'=>0,
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

class Cms_moduls_groupSeeder extends Seeder {

    public function run()
    {        

        $data = [
            ['created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Public','sorting_group'=>1,'is_group'=>0,'icon_group'=>'fa fa-bars'],
            ['created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Articles','sorting_group'=>2,'is_group'=>1,'icon_group'=>'fa fa-bars'],           
            ['created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Users','sorting_group'=>3,'is_group'=>1,'icon_group'=>'fa fa-users'],         
            ['created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Setting','sorting_group'=>4,'is_group'=>1,'icon_group'=>'fa fa-cog']
            ];

        foreach($data as $k=>$d) {
            if(DB::table('cms_moduls_group')->where('nama_group',$d['nama_group'])->count()) {
                unset($data[$k]);
            }
        }

        DB::table('cms_moduls_group')->insert($data);

    }

}

class Cms_usersSeeder extends Seeder {

    public function run()
    {        
        
        if(DB::table('cms_users')->count() == 0) {
            $password = \Hash::make('123456');
            $cms_users = DB::table('cms_users')->insert(array(
                // 'id'=>1,
                'created_at'=>date('Y-m-d H:i:s'),
                'name' => 'Super Admin',
                'photo' => 'vendor/crudbooster/avatar.jpg',
                'email' => 'admin@crudbooster.com',
                'password' => $password,
                'id_cms_privileges' => 1,
                'id_cms_companies' => 1,
                'status'=>'Active'
            ));
            $cms_users = DB::table('cms_users')->insert(array(
                // 'id'=>2,
                'created_at'=>date('Y-m-d H:i:s'),
                'name' => 'Member',
                'photo' => 'vendor/crudbooster/avatar.jpg',
                'email' => 'member@crudbooster.com',
                'password' => $password,
                'id_cms_privileges' => 2,
                'id_cms_companies' => 1,
                'status'=>'Active'
            ));
        }            

    }

}

class Cms_companiesSeeder extends Seeder {

    public function run()
    {        

        if(DB::table('cms_companies')->count() == 0) {
            DB::table('cms_companies')->insert(
            [
            // 'id'=>1,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'CRUDBooster',
            'address'=>'Lorem ipsum Commodo consequat anim et proident cillum.',
            'phone'=>'+6210101010',
            'photo'=>'vendor/crudbooster/avatar.jpg',
            'email'=>'youremail@example.com',
            'description'=>'Lorem ipsum Labore eu consectetur cupidatat sint.',
            'is_primary'=>1
            ]
            );
        }        

    }

}

class Cms_dashboardSeeder extends Seeder {

    public function run()
    {        

        if(DB::table('cms_dashboard')->count() == 0) {
            DB::table('cms_dashboard')->insert([
            [
            // 'id'=>1,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Total Users',
            'id_cms_privileges'=>1,
            'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"3";s:5:"label";s:11:"Total Users";s:4:"icon";s:18:"ion-person-stalker";s:5:"color";s:3:"red";s:10:"table_name";s:9:"cms_users";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
            ],
            [
            // 'id'=>2,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Total Companies',
            'id_cms_privileges'=>1,
            'content'=>'a:8:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"4";s:5:"label";s:13:"Total Company";s:5:"color";s:6:"yellow";s:10:"table_name";s:13:"cms_companies";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
            ],
            [
            // 'id'=>3,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Total Articles',
            'id_cms_privileges'=>1,
            'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"5";s:5:"label";s:14:"Total Articles";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:4:"aqua";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
            ],
            [
            // 'id'=>4,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Total Pages',
            'id_cms_privileges'=>1,
            'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"6";s:5:"label";s:11:"Total Pages";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:5:"green";s:10:"table_name";s:9:"cms_pages";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
            ],
            [
            // 'id'=>5,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Articles By Date',
            'id_cms_privileges'=>1,
            'content'=>'a:10:{s:4:"type";s:10:"chart_line";s:2:"id";s:2:"11";s:5:"label";s:16:"Articles By Date";s:5:"color";s:6:"yellow";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"DATE(created_at) as tanggal";}'
            ],
            [
            // 'id'=>6,
            'created_at'=>date('Y-m-d H:i:s'),
            'name'=>'Articles Bar',
            'id_cms_privileges'=>1,
            'content'=>'a:10:{s:4:"type";s:9:"chart_bar";s:2:"id";s:2:"14";s:5:"label";s:12:"Articles Bar";s:5:"color";s:5:"green";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"date(created_at) as tanggal";}'
            ]
            ]);
        }

    
    }

}
