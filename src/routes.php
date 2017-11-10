<?php

/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

/* ROUTER FOR WEB */


// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', '\crocodicstudio\crudbooster\middlewares\CBBackend'],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () {
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
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace){

    /* DO NOT EDIT THESE BELLOW LINES */
    CRUDBooster::routeController('notifications', 'AdminNotificationsController', $namespace);
    CRUDBooster::routeController('users', 'AdminUsersController');
});

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('email-templates', 'AdminEmailTemplatesController', $namespace);
    CRUDBooster::routeController('logs', 'AdminLogsController', $namespace);
});

