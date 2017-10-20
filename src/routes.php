<?php

/* ROUTER FOR API GENERATOR */
$namespace = '\crocodicstudio\crudbooster\controllers';

Route::group(['middleware' => ['api', '\crocodicstudio\crudbooster\middlewares\CBAuthAPI'], 'namespace' => 'App\Http\Controllers'], function () {
    //Router for custom api defeault

    $dir = scandir(base_path("app/Http/Controllers"));
    foreach ($dir as $v) {
        $v = str_replace('.php', '', $v);
        $names = array_filter(preg_split('/(?=[A-Z])/', str_replace('Controller', '', $v)));
        $names = strtolower(implode('_', $names));

        if (substr($names, 0, 4) == 'api_') {
            $names = str_replace('api_', '', $names);
            Route::any('api/'.$names, $v.'@execute_api');
        }
    }
});

/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('api/doc', ['uses' => 'AdminApiGeneratorController@apiDocumentation', 'as' => 'apiDocumentation']);
    Route::get('download-documentation-postman', ['uses' => 'AdminApiGeneratorController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

/* ROUTER FOR WEB */


// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', '\crocodicstudio\crudbooster\middlewares\CBBackend'],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => 'App\Http\Controllers',
], function () {

    if (Request::is(cbConfig('ADMIN_PATH'))) {
        $menus = DB::table('cms_menus')->where('is_dashboard', 1)->first();
        if ($menus) {
            if ($menus->type == 'Statistic') {
                Route::get('/', '\\crocodicstudio\\crudbooster\\StatisticModule\\AdminStatisticBuilderController@getDashboard');
            } elseif ($menus->type == 'Module') {
                $module = CRUDBooster::first('cms_moduls', ['path' => $menus->path]);
                Route::get('/', $module->controller.'@getIndex');
            } elseif ($menus->type == 'Route') {
                $action = str_replace("Controller", "Controller@", $menus->path);
                $action = str_replace(['Get', 'Post'], ['get', 'post'], $action);
                Route::get('/', $action);
            } elseif ($menus->type == 'Controller & Method') {
                Route::get('/', $menus->path);
            } elseif ($menus->type == 'URL') {
                redirect($menus->path);
            }
        }
    }

    try {
        $moduls = DB::table('cms_moduls')->where('path', '!=', '')->where('controller', '!=', '')->where('is_protected', 0)->get();
        foreach ($moduls as $v) {
            CRUDBooster::routeController($v->path, $v->controller);
        }
    } catch (Exception $e) {
    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBBackend::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace){

    /* DO NOT EDIT THESE BELLOW LINES */
    if (Request::is(cbConfig('ADMIN_PATH'))) {
        $menus = DB::table('cms_menus')->where('is_dashboard', 1)->first();
        if (! $menus) {
            CRUDBooster::routeController('/', '\crocodicstudio\crudbooster\AuthModule\AuthController');
        }
    }

    CRUDBooster::routeController('file-manager', $namespace);
    CRUDBooster::routeController('notifications', 'AdminNotificationsController', $namespace);
    CRUDBooster::routeController('users', 'AdminUsersController', $namespace);
    try {
        $master_controller = glob(__DIR__.'/controllers/*.php');
        foreach ($master_controller as &$file) {
            $file = str_replace('.php', '', basename($file));
        }

        $moduls = DB::table('cms_moduls')->whereIn('controller', $master_controller)->get();

        foreach ($moduls as $module) {
            if (@$module->path && @$module->controller) {
                CRUDBooster::routeController($module->path, $module->controller, $namespace);
            }
        }
    } catch (Exception $e) {

    }
});

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('modules', 'AdminModulesController', $namespace);
    CRUDBooster::routeController('statistic-builder', 'AdminStatisticBuilderController', '\crocodicstudio\crudbooster\StatisticModule');
    CRUDBooster::routeController('file-manager', 'AdminFileManagerController', $namespace);
    CRUDBooster::routeController('menus', 'AdminMenusController', $namespace);
    CRUDBooster::routeController('email-templates', 'AdminEmailTemplatesController', $namespace);
    CRUDBooster::routeController('api-generator', 'AdminApiGeneratorController', $namespace);
    CRUDBooster::routeController('logs', 'AdminLogsController', $namespace);
});

