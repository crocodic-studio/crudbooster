<?php

// Developer Backend Middleware
Route::group(['middleware' => ['web',\crocodicstudio\crudbooster\middlewares\CBDeveloper::class],
    'prefix'=>"developer/".getSetting('developer_path'),
    'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routeController("modules", "\crocodicstudio\crudbooster\controllers\DeveloperModulesController");
    cb()->routeController("menus", "\crocodicstudio\crudbooster\controllers\DeveloperMenusController");
    cb()->routeController("roles","\crocodicstudio\crudbooster\controllers\DeveloperRolesController");
    cb()->routeController("users","\crocodicstudio\crudbooster\controllers\DeveloperUsersController");
    cb()->routeController("plugins","\crocodicstudio\crudbooster\controllers\DeveloperPluginStoreController");
    cb()->routeController("mail","\crocodicstudio\crudbooster\controllers\DeveloperMailController");
    cb()->routeController("security","\crocodicstudio\crudbooster\controllers\DeveloperSecurityController");
    cb()->routeController("themes","\crocodicstudio\crudbooster\controllers\DeveloperThemesController");
    cb()->routeController("appearance","\crocodicstudio\crudbooster\controllers\DeveloperAppearanceController");
    cb()->routeController("miscellaneous","\crocodicstudio\crudbooster\controllers\DeveloperMiscellaneousController");
    cb()->routePost("skip-tutorial","DeveloperDashboardController@postSkipTutorial");
    cb()->routeGet("/","DeveloperDashboardController@getIndex");
});

// Developer Auth Middleware
Route::group(['middleware' => ['web'],
    'prefix'=>"developer/".getSetting('developer_path'),
    'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routePost("login","AdminAuthController@postLoginDeveloper");
    cb()->routeGet("login","AdminAuthController@getLoginDeveloper");
    cb()->routeGet("logout","AdminAuthController@getLogoutDeveloper");
});

// Routing without any middleware
Route::group(['middleware' => ['web'], 'namespace' => '\crocodicstudio\crudbooster\controllers'], function () {
    if(getSetting("AUTO_REDIRECT_TO_LOGIN")) {
        cb()->routeGet("/","AdminAuthController@getRedirectToLogin");
    }
});

// Routing without any middleware with admin prefix
Route::group(['middleware' => ['web'], 'prefix' => cb()->getAdminPath(), 'namespace' => 'crocodicstudio\crudbooster\controllers'], function () {
    cb()->routeGet('logout', "AdminAuthController@getLogout");

    if(!getSetting("DISABLE_LOGIN")) {
        cb()->routePost('login', "AdminAuthController@postLogin");
        cb()->routeGet('login', "AdminAuthController@getLogin");
        cb()->routeGet("login-verification","AdminAuthController@getLoginVerification");
        cb()->routePost("submit-login-verification","AdminAuthController@postSubmitLoginVerification");
    }

    if(getSetting("enable_forget")) {
        cb()->routePost("forget","AdminAuthController@postForget");
    }

    if(getSetting("enable_register")) {
        cb()->routePost("register","AdminAuthController@postRegister");
    }
});

// Routing package controllers
cb()->routeGroupBackend(function () {
    cb()->routeController('profile', '\crocodicstudio\crudbooster\controllers\AdminProfileController');
});

// Auto Routing for App\Http\Controllers
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBBackend::class],
    'prefix' => cb()->getAdminPath(),
    'namespace' => 'App\Http\Controllers',
], function () {

    if (Request::is(cb()->getAdminPath())) {
        if($dashboard = getSetting("dashboard_controller")) {
            cb()->routeGet("/", $dashboard."@getIndex");
        }else{
            cb()->routeGet("/", "\crocodicstudio\crudbooster\controllers\AdminDashboardController@getIndex");
        }
    }

    $controllers = glob(app_path('Http/Controllers/Admin*Controller.php'));

    foreach($controllers as $controller) {
        $controllerName = basename($controller);
        $controllerName = rtrim($controllerName,".php");
        $className = '\App\Http\Controllers\\'.$controllerName;
        $controllerClass = new $className();
        if(method_exists($controllerClass, 'cbInit')) {
            cb()->routeController($controllerClass->getData('permalink'), $controllerName);
        }
    }
});
