<?php

use crocodicstudio\crudbooster\CBCoreModule\CbRouter;
use crocodicstudio\crudbooster\middlewares\CBBackend;

Route::group(['middleware' => ['web']], function () {
    Route::get(cbAdminPath(), '\crocodicstudio\crudbooster\controllers\DashboardController@index')->name('CbDashboard');
});

$namespace = cbControllersNS();
/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () {
    CbRouter::routeController('users', 'AdminUsersController');
    try {
        $modules = DB::table('cms_moduls')->where('path', '!=', '')->where('controller', '!=', '')->where('is_protected', 0)->get();
        foreach ($modules as $module) {
            CbRouter::routeController($module->path, $module->controller);
        }
    } catch (Exception $e) {
        // we skip if routing was not successful
    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () {
    Route::post('{module}/do-upload-import-data', 'FileController@uploadImportData')->name('UploadImportData');
    Route::post('{module}/upload-summernote', 'FileController@uploadSummernote')->name('UploadImportData');
    Route::post('{module}/upload-file', 'FileController@uploadFile')->name('UploadImportData');
    Route::post('{module}/done-import', 'FileController@doneImport')->name('doneImportData');
});

