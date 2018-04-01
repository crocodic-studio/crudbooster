<?php

use crocodicstudio\crudbooster\middlewares\CBBackend;

$namespace = '\crocodicstudio\crudbooster\controllers';
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
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () {
    Route::post('{module}/do-upload-import-data', ['uses' => 'FileController@uploadImportData', 'as' => 'UploadImportData',]);
    Route::post('{module}/upload-summernote', ['uses' => 'FileController@uploadSummernote', 'as' => 'UploadImportData',]);
    Route::post('{module}/upload-file', ['uses' => 'FileController@uploadFile', 'as' => 'UploadImportData',]);
    Route::post('{module}/done-import', ['uses' => 'FileController@doneImport', 'as' => 'doneImportData',]);
});

