<?php


/* ROUTER FOR API GENERATOR */
$namespace = '\crocodicstudio\crudbooster\controllers';

Route::group(['middleware'=>['api','\crocodicstudio\crudbooster\middlewares\CBAuthAPI'],'namespace'=>$namespace], function () {
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


/* ROUTER FOR WEB */
Route::group(['middleware'=>['web'],'prefix'=>config('crudbooster.ADMIN_PATH'),'namespace'=>$namespace], function () {
		
	Route::post('unlock-screen', ['uses'=>'AdminController@postUnlockScreen','as'=>'postUnlockScreen']);
	Route::get('lock-screen', ['uses'=>'AdminController@getLockscreen','as'=>'getLockScreen']);	

	Route::post('forgot',['uses'=>'AdminController@postForgot','as'=>'postForgot']);
	Route::get('forgot',['uses'=>'AdminController@getForgot','as'=>'getForgot']);

	Route::post('register', ['uses'=>'AdminController@postRegister','as'=>'postRegister']);
	Route::get('register', ['uses'=>'AdminController@getRegister','as'=>'getRegister']);

	Route::get('logout', ['uses'=>'AdminController@getLogout','as'=>'getLogout']);		

	
	Route::post('login', ['uses'=>'AdminController@postLogin','as'=>'postLogin']);	
	Route::get('login', ['uses'=>'AdminController@getLogin','as'=>'getLogin']);
	/* ROUTE FOR UPLOADS */
	Route::get('uploads/{folder}/{filename}', ['as'=>'getUploads', function ($folder,$filename)
	{
	    $path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!File::exists($path)) abort(404);

	    $file             = File::get($path);
	    $type             = File::mimeType($path);
	    $response         = Response::make($file, 200);
	    $response->header("Content-Type", $type);

	    $seconds_to_cache = 3600 * (24*30);
	    $gmt_mtime        = gmdate('D, d M Y H:i:s', time() ) . ' GMT';
		$ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";		
		$response->header("Expires",$ts);
		$response->header("Last-Modified",$gmt_mtime);
		$response->header("Pragma","cache");
		$response->header("Cache-Control","max-age=$seconds_to_cache, must-revalidate");
	    return $response;
	}]);


});


/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group(['middleware'=>['web','\crocodicstudio\crudbooster\middlewares\CBBackend'],'prefix'=>config('crudbooster.ADMIN_PATH'),'namespace'=>$namespace], function () {

	/* DO NOT EDIT THESE BELLOW LINES */

	Route::get('/', ['uses'=>'AdminController@getIndex','as'=>'AdminControllerGetIndex']);
	Route::post('save-cms-dashboard',['uses'=>'AdminController@postSaveCmsDashboard','as'=>'AdminControllerPostSaveCmsDashboard']);
	Route::get('users-confirmation',['uses'=>'AdminController@getUsersConfirmation','as'=>'AdminControllerGetUsersConfirmation']);
	Route::get('unset-dashboard-config-mode',['uses'=>'AdminController@getUnsetDashboardConfigMode','as'=>'AdminControllerGetUnsetDashboardConfigMode']);
	Route::get('remove-cms-dashboard',['uses'=>'AdminController@getRemoveCmsDashboard','as'=>'AdminControllerGetRemoveCmsDashboard']);
	Route::get('statistic-dashboard/{id}',['uses'=>'AdminController@getStatisticDashboard','as'=>'AdminControllerGetStatisticDashboard']);
	Route::get('setting-stat-number',['uses'=>'AdminController@getSettingStatNumber','as'=>'AdminControllerGetSettingStatNumber']);
	Route::get('setting-chart-line',['uses'=>'AdminController@getSettingChartLine','as'=>'AdminControllerGetSettingChartLine']);
	Route::get('setting-chart-bar',['uses'=>'AdminController@getSettingChartBar','as'=>'AdminControllerGetSettingChartBar']);
	Route::get('setting-chart-donut',['uses'=>'AdminController@getSettingChartDonut','as'=>'AdminControllerGetSettingChartDonut']);
	Route::get('setting-chart-datatable',['uses'=>'AdminController@getSettingChartDatatable','as'=>'AdminControllerGetSettingDatatable']);
	Route::get('api_generator/column-table/{table}/{action}',['uses'=>'ApiCustomController@getColumnTable','as'=>'ApiCustomControllerGetColumnTable']);
	Route::post('api_generator/save-api-custom',['uses'=>'ApiCustomController@postSaveApiCustom','as'=>'ApiCustomControllerPostSaveApiCustom']);
	Route::get('api_generator/generate-screet-key',['uses'=>'ApiCustomController@getGenerateScreetKey','as'=>'ApiCustomControllerGetGenerateScreetKey']);
	Route::get('api_generator/delete-api/{id}',['uses'=>'ApiCustomController@getDeleteApi','as'=>'ApiCustomControllerGetDeleteApi']);
	Route::get('api_generator/edit/{id}',['uses'=>'ApiCustomController@getEdit','as'=>'ApiCustomControllerGetEdit']);
	Route::post('api_generator/edit-save/{id}',['uses'=>'ApiCustomController@postEditSave','as'=>'ApiCustomControllerPostEditSave']);
	Route::get('notifications/latest-json',['uses'=>'NotificationsController@getLatestJson','as'=>'NotificationsControllerGetLatestJson']);
	Route::get('notifications/read/{id}',['uses'=>'NotificationsController@getRead','as'=>'NotificationsControllerGetRead']);
	Route::get('profile',['uses'=>'UsersController@getProfile','as'=>'UsersControllerGetProfile']);
	Route::get('cms_moduls/find-last-sorting/{id}',['uses'=>'ModulsController@getFindLastSorting','as'=>'ModulsControllerGetFindLastSorting']);
	Route::get('cms_moduls/arr-sorting/{id}/{tipe}',['uses'=>'ModulsController@getArrSorting','as'=>'ModulsControllerGetArrSorting']);
	Route::get('module_generator/find-last-sorting',['uses'=>'ModulsGroupController@getFindLastSorting','as'=>'ModulsGroupControllerrGetFindLastSorting']);
	Route::get('module_generator/arr-sorting/{id}/{tipe}',['uses'=>'ModulsGroupControllerr@getArrSorting','as'=>'ModulsGroupControllerrGetArrSorting']);
	
	try{
		$moduls = DB::table('cms_moduls')->get();
		foreach($moduls as $v) {
			if(@$v->path && @$v->controller) {
				Route::get($v->path,['uses'=>$v->controller.'@getIndex','as'=>$v->controller.'GetIndex']);
				Route::get($v->path.'/data-tables',['uses'=>$v->controller.'@getDataTables','as'=>$v->controller.'GetDataTables']);
				Route::get($v->path.'/current-data-tables',['uses'=>$v->controller.'@getAdd','as'=>$v->controller.'GetAdd']);
				Route::get($v->path.'/export-data',['uses'=>$v->controller.'@getExoirtData','as'=>$v->controller.'GetExportData']);
				Route::post($v->path.'/export-data',['uses'=>$v->controller.'@postExportData','as'=>$v->controller.'PostExportData']);
				Route::get($v->path.'/find-data',['uses'=>$v->controller.'@getFindData','as'=>$v->controller.'GetFindData']);
				Route::get($v->path.'/find-group-data',['uses'=>$v->controller.'@getFindGroupData','as'=>$v->controller.'GetFindGroupData']);
				Route::get($v->path.'/add',['uses'=>$v->controller.'@getAdd','as'=>$v->controller.'GetAdd']);
				Route::post($v->path.'/add-save',['uses'=>$v->controller.'@postAddSave','as'=>$v->controller.'PostAddSave']);				
				Route::get($v->path.'/edit/{id}',['uses'=>$v->controller.'@getEdit','as'=>$v->controller.'GetEdit']);
				Route::get($v->path.'/detail/{id}',['uses'=>$v->controller.'@getDetail','as'=>$v->controller.'GetDetail']);				
				Route::post($v->path.'/edit-save/{id}',['uses'=>$v->controller.'@postEditSave','as'=>$v->controller.'PostEditSave']);
				Route::get($v->path.'/delete/{id}',['uses'=>$v->controller.'@getDelete','as'=>$v->controller.'GetDelete']);
				Route::post($v->path.'/delete-selected',['uses'=>$v->controller.'@postDeleteSelected','as'=>$v->controller.'PostDeleteSelected']);
				Route::get($v->path.'/delete-image',['uses'=>$v->controller.'@getDeleteImage','as'=>$v->controller.'GetDeleteImage']);
				Route::get($v->path.'/delete-filemanager',['uses'=>$v->controller.'@getDeleteFilemanager','as'=>$v->controller.'GetDeleteFilemanager']);
			}						
		}
	}catch(Exception $e) {

	}	
});

