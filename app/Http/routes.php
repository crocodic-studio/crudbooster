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

/* IF YOU WANT ADD NEW ROUTER, ADD AFTER THIS LINE */
 





/* END OF YOUR OWN ROUTER */

/* SETTING ROUTER WITH CSRF */
$router->group(['middleware' => 'csrf'], function($router)
{	

	/* YOUR OWN ROUTER IF ANY */





	


	/* END YOUR OWN ROUTER */

	//Image Reader 
	Route::get('uploads/{folder}/{filename}', function ($folder,$filename)
	{
	    $path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!File::exists($path)) abort(404);

	    $file = File::get($path);
	    $type = File::mimeType($path);
	    $response = Response::make($file, 200);
	    $response->header("Content-Type", $type);

	    $seconds_to_cache = 3600 * (24*30);
	    $gmt_mtime = gmdate('D, d M Y H:i:s', time() ) . ' GMT';

		$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";		
		$response->header("Expires",$ts);
		$response->header("Last-Modified",$gmt_mtime);
		$response->header("Pragma","cache");
		$response->header("Cache-Control","max-age=$seconds_to_cache, must-revalidate");
	    return $response;
	});

	/* DO NOT EDIT THESE BELLOW LINES */
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
	/* END */	

	//No Need Edit Bellow Router
	//Router for admin dashboard,login,logout,etc
	Route::controller('/admin','AdminController');	

	//Router for custom api defeault
	Route::controller('/apis','CustomApiController');

	//Router for front end
	Route::controller('/','FrontController');
});
