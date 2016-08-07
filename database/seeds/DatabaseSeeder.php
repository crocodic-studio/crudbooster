<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('Cms_usersSeeder');
		$this->call('Cms_companiesSeeder');
		$this->call('Cms_dashboardSeeder');
		$this->call('Cms_moduls_groupSeeder');
		$this->call('Cms_modulsSeeder');
		$this->call('Cms_privilegesSeeder');
		$this->call('Cms_privileges_rolesSeeder');
		$this->call('Cms_settingsSeeder');
		
		$this->command->info('Seed Data Success !');
	}

}

class Cms_settingsSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_settings')->delete();

        DB::table('cms_settings')->insert([
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'appname','content'=>'CRUDBooster','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'email_sender','content'=>'support@crudbooster.com','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'app_lockscreen_timeout','content'=>'60','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'default_paper_size','content'=>'Legal','content_input_type'=>'text','dataenum'=>NULL,'helper'=>'Paper size, ex : A4, Legal, etc'],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'smtp_driver','content'=>'mail','content_input_type'=>'select','dataenum'=>'smtp,mail,sendmail','helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'smtp_host','content'=>'','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'smtp_port','content'=>'','content_input_type'=>'text','dataenum'=>NULL,'helper'=>'default 25'],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'smtp_username','content'=>'','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'smtp_password','content'=>'','content_input_type'=>'text','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'logo','content'=>'','content_input_type'=>'upload','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'favicon','content'=>'','content_input_type'=>'upload','dataenum'=>NULL,'helper'=>NULL],
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'api_debug_mode','content'=>'','content_input_type'=>'select','dataenum'=>'true,false','helper'=>NULL],        
        ['created_at'=>date('Y-m-d H:i:s'),'name'=>'google_api_key','content'=>'','content_input_type'=>'upload','dataenum'=>NULL,'helper'=>NULL]
        ]);
    }
}

class Cms_privileges_rolesSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_privileges_roles')->delete();

        $modules = DB::table('cms_moduls')->get();
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
			}

        	DB::table('cms_privileges_roles')->insert([
        		'created_at'=>date('Y-m-d H:i:s'),
        		'is_visible'=>$is_visible,
        		'is_create'=>$is_create,
        		'is_edit'=>$is_edit,
        		'is_delete'=>$is_delete,
        		'is_read'=>$is_read,
        		'id_cms_privileges'=>1,
        		'id_cms_moduls'=>$module->id
        		]);
        }
    }
}

class Cms_privilegesSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_privileges')->delete();
        DB::table('cms_privileges')->insert([
        	'id'=>1,
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Super Administrator',
        	'is_superadmin'=>1,
        	'theme_color'=>'skin-blue-light'
        	]);
    }
}

class Cms_modulsSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_moduls')->delete();

        /* 
			1 = Public
			2 = Articles
			3 = Master Data
			4 = Setting			
        */

        DB::table('cms_moduls')->insert([
        [
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Privileges',
        	'icon'=>'fa fa-cog',
        	'path'=>'admin/privileges',
        	'table_name'=>'cms_privileges',
        	'controller'=>'PrivilegesController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>6,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>4,
        	'is_softdelete'=>0,
        	'is_active'=>1
        ],
        [
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Privileges Roles',
        	'icon'=>'fa fa-cog',
        	'path'=>'admin/privileges_roles',
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
        	'path'=>'admin/cms_moduls',
        	'table_name'=>'cms_moduls',
        	'controller'=>'ModulsController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>2,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>4,
        	'is_softdelete'=>0,
        	'is_active'=>0
        ],
        [	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Users',
        	'icon'=>'fa fa-users',
        	'path'=>'admin/users',
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
        	'path'=>'admin/settings',
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
        	'path'=>'admin/module_generator',
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
        	'path'=>'admin/api_generator',
        	'table_name'=>'',
        	'controller'=>'ApiGeneratorController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>1,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>4,
        	'is_softdelete'=>0,
        	'is_active'=>1
        ],[	
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'API Management',
        	'icon'=>'fa fa-bars',
        	'path'=>'admin/api_management',
        	'table_name'=>'cms_apicustom',
        	'controller'=>'ApiGeneratorController',
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
        	'path'=>'admin/companies',
        	'table_name'=>'cms_companies',
        	'controller'=>'CompaniesController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>1,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>3,
        	'is_softdelete'=>0,
        	'is_active'=>1
        ],[	
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Logs',
        	'icon'=>'fa fa-flag-o',
        	'path'=>'admin/logs',
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
        	'path'=>'admin/cms_menus_groups',
        	'table_name'=>'cms_menus_groups',
        	'controller'=>'AdminCmsMenusGroupsController',
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
        	'path'=>'admin/cms_menus',
        	'table_name'=>'cms_menus',
        	'controller'=>'AdminCmsMenusController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>1,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>4,
        	'is_softdelete'=>0,
        	'is_active'=>0
        ],[	
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Articles',
        	'icon'=>'fa fa-bars',
        	'path'=>'admin/cms_posts',
        	'table_name'=>'cms_posts',
        	'controller'=>'AdminCmsPostsController',
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
        	'path'=>'admin/cms_posts_categories',
        	'table_name'=>'cms_posts_categories',
        	'controller'=>'AdminCmsPostsCategoriesController',
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
        	'path'=>'admin/cms_pages',
        	'table_name'=>'cms_pages',
        	'controller'=>'AdminCmsPagesController',
        	'sql_where'=>'','sql_orderby'=>'',
        	'sorting'=>1,
        	'limit_data'=>NULL,
        	'id_cms_moduls_group'=>1,
        	'is_softdelete'=>0,
        	'is_active'=>1
        ]
        

        	]);
    }

}

class Cms_moduls_groupSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_moduls_group')->delete();

        DB::table('cms_moduls_group')->insert([
        	['id'=>1,'created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Public','sorting_group'=>1,'is_group'=>0,'icon_group'=>'fa fa-bars'],
        	['id'=>2,'created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Articles','sorting_group'=>2,'is_group'=>1,'icon_group'=>'fa fa-bars'],
        	['id'=>3,'created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Master Data','sorting_group'=>3,'is_group'=>1,'icon_group'=>'fa fa-database'],
        	['id'=>4,'created_at'=>date('Y-m-d H:i:s'),'nama_group'=>'Setting','sorting_group'=>4,'is_group'=>1,'icon_group'=>'fa fa-cog']
        	]);

    }

}

class Cms_usersSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_users')->delete();

        $password = \Hash::make('123456');
		$cms_users = DB::table('cms_users')->insert(array(
		  	'id'=>1,
		  	'created_at'=>date('Y-m-d H:i:s'),
		    'name' => 'Super Admin',
		    'photo' => 'default/avatar.jpg',
		    'email' => 'admin@crudbooster.com',
		    'password' => $password,
		    'id_cms_privileges' => 1,
		    'id_cms_companies' => 1
		));

  //       $faker = Faker\Factory::create();
		// for ($i = 1; $i < 5; $i++)
		// {
		//   $password = \Hash::make($faker->password);
		//   $cms_users = DB::table('cms_users')->insert(array(
		//   	'id'=>,
		//   	'created_at'=>date('Y-m-d H:i:s'),
		//     'name' => $faker->name,
		//     'email' => $faker->email,
		//     'password' => $password
		//   ));
		// }

    }

}

class Cms_companiesSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_companies')->delete();

        DB::table('cms_companies')->insert(
        	[
        	'id'=>1,
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'CRUDBooster',
        	'address'=>'Lorem ipsum Commodo consequat anim et proident cillum.',
        	'phone'=>'+6210101010',
        	'email'=>'youremail@example.com',
        	'description'=>'Lorem ipsum Labore eu consectetur cupidatat sint.',
        	'is_primary'=>1
        	]
        	);

    }

}

class Cms_dashboardSeeder extends Seeder {

    public function run()
    {
        DB::table('cms_dashboard')->delete();

        DB::table('cms_dashboard')->insert([
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Total Users',
        	'id_cms_privileges'=>1,
        	'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"3";s:5:"label";s:11:"Total Users";s:4:"icon";s:18:"ion-person-stalker";s:5:"color";s:3:"red";s:10:"table_name";s:9:"cms_users";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
        	],
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Total Companies',
        	'id_cms_privileges'=>1,
        	'content'=>'a:8:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"4";s:5:"label";s:13:"Total Company";s:5:"color";s:6:"yellow";s:10:"table_name";s:13:"cms_companies";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
        	],
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Total Articles',
        	'id_cms_privileges'=>1,
        	'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"5";s:5:"label";s:14:"Total Articles";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:4:"aqua";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
        	],
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Total Pages',
        	'id_cms_privileges'=>1,
        	'content'=>'a:9:{s:4:"type";s:16:"statistic_number";s:2:"id";s:1:"6";s:5:"label";s:11:"Total Pages";s:4:"icon";s:23:"ion-arrow-graph-up-left";s:5:"color";s:5:"green";s:10:"table_name";s:9:"cms_pages";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";}'
        	],
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Articles By Date',
        	'id_cms_privileges'=>1,
        	'content'=>'a:10:{s:4:"type";s:10:"chart_line";s:2:"id";s:2:"11";s:5:"label";s:16:"Articles By Date";s:5:"color";s:6:"yellow";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"DATE(created_at) as tanggal";}'
        	],
        	[
        	'created_at'=>date('Y-m-d H:i:s'),
        	'name'=>'Articles Bar',
        	'id_cms_privileges'=>1,
        	'content'=>'a:10:{s:4:"type";s:9:"chart_bar";s:2:"id";s:2:"14";s:5:"label";s:12:"Articles Bar";s:5:"color";s:5:"green";s:5:"width";s:4:"half";s:10:"table_name";s:9:"cms_posts";s:14:"aggregate_type";s:5:"count";s:6:"column";s:2:"id";s:9:"sql_where";s:0:"";s:12:"sql_group_by";s:27:"date(created_at) as tanggal";}'
        	]
        	]);

    }

}
