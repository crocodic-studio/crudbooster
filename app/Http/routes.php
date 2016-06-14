<?php
/* LARAVEL AUTO ROUTER */
$dir = base_path("app/Http/Controllers");
$controllers = scandir($dir);
$controllersnew = array();
foreach($controllers as $v) {
	if($v!='.' && $v!='..' && $v!='ControllerMaster') {
		$v     = str_replace('.php','',$v);
		$names = array_filter(preg_split('/(?=[A-Z])/',str_replace('Controller','',$v)));		
		$names = strtolower(implode('_',$names));	
		
		if(substr($names,0,4)=='api_') {	
			$names = str_replace('api_','',$names);	
			Route::any('api/'.$names,$v.'@execute_api');
		}
	
	}
}
 

 
/* SETTING ROUTER DEFAULT CMS */
$router->group(['middleware' => 'csrf'], function($router)
{
	if(Schema::hasTable('cms_moduls')) {
		$moduls = DB::table('cms_moduls')->get();
		foreach($moduls as $v) {
			if(@$v->path && @$v->controller) {
				if(@file_exists(base_path("app/Http/Controllers/".$v->controller.".php")) || @file_exists(base_path("app/Http/Controllers/ControllerMaster/".$v->controller.".php")) ) {
					Route::controller($v->path,$v->controller);
				}
			}						
		}	
	}
	

    // Protected routes
	Route::controller('/updater', 'UpdaterController'); 
	Route::controller('/admin/cms_moduls', 'ModulsController'); 
	Route::controller('/admin','AdminController');
	Route::controller('upload_virtual','FileHandelController');
	Route::controller('/','FrontController');
});
