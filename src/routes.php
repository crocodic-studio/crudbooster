<?php

// Developer Backend Middleware
Route::group(['middleware' => ['web',\crocodicstudio\crudbooster\middlewares\CBDeveloper::class],
    'prefix'=>cbConfig('DEV_PATH'),
    'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routeController("modules", "DeveloperModulesController",'crocodicstudio\crudbooster\controllers');
    cb()->routeController("menus", "DeveloperMenusController",'crocodicstudio\crudbooster\controllers');
    cb()->routeController("roles","DeveloperRolesController",'crocodicstudio\crudbooster\controllers');
    cb()->routeGet("/","DeveloperDashboardController@getIndex");
});

// Developer Auth Middleware
Route::group(['middleware' => ['web'],
    'prefix'=>cbConfig('DEV_PATH'),
    'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routePost("login","AdminAuthController@postLoginDeveloper");
    cb()->routeGet("login","AdminAuthController@getLoginDeveloper");
    cb()->routeGet("logout","AdminAuthController@getLogoutDeveloper");
});

// Routing without any middleware
Route::group(['middleware' => ['web'], 'namespace' => '\crocodicstudio\crudbooster\controllers'], function () {
    cb()->routeGet('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

// Routing without any middleware with admin prefix
Route::group(['middleware' => ['web'], 'prefix' => cbConfig('ADMIN_PATH'), 'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routeGet('logout', ['uses' => 'AdminAuthController@getLogout', 'as' => 'getLogout']);
    cb()->routePost('login', ['uses' => 'AdminAuthController@postLogin', 'as' => 'postLogin']);
    cb()->routeGet('login', ['uses' => 'AdminAuthController@getLogin', 'as' => 'getLogin']);
});

// Routing package controllers
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBBackend::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => 'crocodicstudio\crudbooster\controllers',
], function () {
    cb()->routeController('profile', 'AdminProfileController');
});

// Auto Routing for App\Http\Controllers
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBBackend::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => 'App\Http\Controllers',
], function () {

    if (Request::is(cbConfig('ADMIN_PATH'))) {
        if($dashboard = cbConfig("ADMIN_DASHBOARD_CONTROLLER")) {
            cb()->routeGet("/", $dashboard);
        }else{
            cb()->routeGet("/", "crocodicstudio\crudbooster\controllers\AdminDashboardController@getIndex");
        }
    }

    $controllers = glob(app_path('Http/Controllers/Admin*Controller.php'));
    foreach($controllers as $controller) {
        $controllerName = basename($controller);
        $className = '\App\Http\Controllers\\'.$controllerName;
        $controllerClass = new $className();
        if(method_exists($controllerClass, 'cbInit')) {
            cb()->routeController($controllerClass->getData('permalink'), $controllerName);
        }
    }
});
