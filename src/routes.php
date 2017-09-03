<?php


/* ROUTER FOR API GENERATOR */
$namespace = '\crocodicstudio\crudbooster\controllers';

Route::group(['middleware'=>['api','\crocodicstudio\crudbooster\middlewares\CBAuthAPI'],'namespace'=>'App\Http\Controllers'], function () {
	//Router for custom api defeault	

	$dir       = scandir(base_path("app/Http/Controllers"));		
	foreach($dir as $v) {		
		$v     = str_replace('.php','',$v);
		$names = array_filter(preg_split('/(?=[A-Z])/',str_replace('Controller','',$v)));		
		$names = strtolower(implode('_',$names));	
		
		if(substr($names,0,4)=='api_') {	
			$names = str_replace('api_','',$names);	
			Route::any('api/'.$names,$v.'@execute_api');
		}
	}

});

/* ROUTER FOR UPLOADS */
Route::group(['middleware'=>['web'],'namespace'=>$namespace],function() {		
	Route::get('api/doc', ['uses'=>'AdminApiGeneratorController@apiDocumentation','as'=>'apiDocumentation']);	
	Route::get('download-documentation-postman', ['uses'=>'AdminApiGeneratorController@getDownloadPostman','as'=>'downloadDocumentationPostman']);			
	Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses'=>'FileController@getPreview','as'=>'fileControllerPreview']);		
});


/* ROUTER FOR WEB */
Route::group(['middleware'=>['web'],'prefix'=>cbConfig('ADMIN_PATH'),'namespace'=>$namespace], function () {
		
	Route::post('unlock-screen', ['uses'=>'AdminController@postUnlockScreen','as'=>'postUnlockScreen']);
	Route::get('lock-screen', ['uses'=>'AdminController@getLockscreen','as'=>'getLockScreen']);	
	Route::post('forgot',['uses'=>'AdminController@postForgot','as'=>'postForgot']);
	Route::get('forgot',['uses'=>'AdminController@getForgot','as'=>'getForgot']);
	Route::post('register', ['uses'=>'AdminController@postRegister','as'=>'postRegister']);
	Route::get('register', ['uses'=>'AdminController@getRegister','as'=>'getRegister']);
	Route::get('logout', ['uses'=>'AdminController@getLogout','as'=>'getLogout']);			
	Route::post('login', ['uses'=>'AdminController@postLogin','as'=>'postLogin']);	
	Route::get('login', ['uses'=>'AdminController@getLogin','as'=>'getLogin']);		
	
});




// ROUTER FOR OWN CONTROLLER FROM CB
Route::group(['middleware'=>['web','\crocodicstudio\crudbooster\middlewares\CBBackend'],'prefix'=>cbConfig('ADMIN_PATH'),'namespace'=>'App\Http\Controllers'], function () {
		
		if(Request::is(cbConfig('ADMIN_PATH'))) {
			$menus = DB::table('cms_menus')->where('is_dashboard',1)->first();
			if($menus) {
				if($menus->type == 'Statistic') {
					Route::get('/','\crocodicstudio\crudbooster\controllers\AdminStatisticBuilderController@getDashboard');
				}elseif ($menus->type == 'Module') {
					$module = CRUDBooster::first('cms_moduls',['path'=>$menus->path]);
					Route::get('/',$module->controller.'@getIndex');
				}elseif ($menus->type == 'Route') {
					$action = str_replace("Controller","Controller@",$menus->path);
					$action = str_replace(['Get','Post'],['get','post'],$action);
					Route::get('/',$action);
				}elseif ($menus->type == 'Controller & Method') {
					Route::get('/',$menus->path);
				}elseif ($menus->type == 'URL') {
					redirect($menus->path);
				}
			}
		}

		try {
			$moduls = DB::table('cms_moduls')
			->where('path','!=','')
			->where('controller','!=','')
			->where('is_protected',0)->get();			
			foreach($moduls as $v) {						
				CRUDBooster::routeController($v->path,$v->controller);						
			}			
		} catch (Exception $e) {
			
		}			
});



/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group(['middleware'=>['web','\crocodicstudio\crudbooster\middlewares\CBBackend'],'prefix'=>cbConfig('ADMIN_PATH'),'namespace'=>$namespace], function () {

	/* DO NOT EDIT THESE BELLOW LINES */
	if(Request::is(cbConfig('ADMIN_PATH'))) {
		$menus = DB::table('cms_menus')->where('is_dashboard',1)->first();
		if(!$menus) {		
			CRUDBooster::routeController('/','AdminController',$namespace='\crocodicstudio\crudbooster\controllers');	
		}
	}
	
	CRUDBooster::routeController('file-manager',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('notifications','AdminNotificationsController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('users','AdminUsersController',$namespace='\crocodicstudio\crudbooster\controllers');	
	
	try{
		$master_controller = glob(__DIR__.'/controllers/*.php');
		foreach($master_controller as &$m) $m = str_replace('.php','',basename($m));		
		
		$moduls = DB::table('cms_moduls')->whereIn('controller',$master_controller)->get();	
		
		foreach($moduls as $v) {
			if(@$v->path && @$v->controller) {		
				CRUDBooster::routeController($v->path,$v->controller,$namespace='\crocodicstudio\crudbooster\controllers');
			}
		}
	}catch(Exception $e) {
		
	}	
});

Route::group(['middleware'=>['web','\crocodicstudio\crudbooster\middlewares\CBSuperadmin'],'prefix'=>cbConfig('ADMIN_PATH'),'namespace'=>$namespace], function () {
	
	CRUDBooster::routeController('privileges','AdminPrivilegesController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('settings','AdminSettingsController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('modules','AdminModulesController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('statistic-builder','AdminStatisticBuilderController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('file-manager','AdminFileManagerController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('menus','AdminMenusController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('email-templates','AdminEmailTemplatesController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('api-generator','AdminApiGeneratorController',$namespace='\crocodicstudio\crudbooster\controllers');
	CRUDBooster::routeController('logs','AdminLogsController',$namespace='\crocodicstudio\crudbooster\controllers');
});

