<?php

use crocodicstudio\crudbooster\CBCoreModule\Facades\CbRouter;

$namespace = cbControllersNS();
/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\CBCoreModule\middlewares\CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () {
    try {
        $argv = request()->server('argv');
        if (is_array($argv) && isset($argv[1]) && !starts_with($argv[1], 'route:')) {
            return;
        }
        $modules = DB::table('cms_moduls')->where('path', '!=', '')->where('controller', '!=', '')->where('is_protected', 0)->get();
        foreach ($modules as $module) {
            CbRouter::routeController($module->path, $module->controller);
        }
    } catch (Exception $e) {
       dd($e);
    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () {
    Route::post('{module}/do-upload-import-data', 'FileController@uploadImportData')->name('UploadImportData');
    Route::post('{module}/upload-summernote', 'FileController@uploadSummernote')->name('UploadImportData');
    Route::post('{module}/upload-file', 'FileController@uploadFile')->name('UploadImportData');
    Route::post('{module}/done-import', 'FileController@doneImport')->name('doneImportData');
});

