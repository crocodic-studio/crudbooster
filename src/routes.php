<?php


use crocodicstudio\crudbooster\middlewares\CBBackend;

$namespace = '\crocodicstudio\crudbooster\controllers';
/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

/* ROUTER FOR WEB */


// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () {
    CRUDBooster::routeController('users', 'AdminUsersController');
    try {
        $modules = DB::table('cms_moduls')->where('path', '!=', '')->where('controller', '!=', '')->where('is_protected', 0)->get();
        foreach ($modules as $module) {
            CRUDBooster::routeController($module->path, $module->controller);
        }
    } catch (Exception $e) {
    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace){

    /* DO NOT EDIT THESE BELLOW LINES */
    CRUDBooster::routeController('notifications', 'AdminNotificationsController', $namespace);

});

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('email-templates', 'AdminEmailTemplatesController', $namespace);
    CRUDBooster::routeController('logs', 'AdminLogsController', $namespace);
});

