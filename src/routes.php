<?php

use Crocodicstudio\Crudbooster\CBCoreModule\Facades\CbRouter;

$namespace = cbControllersNS();
/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

Route::group(['middleware' => ['web']], function () {
    Route::get(cbAdminPath(), '\Crocodicstudio\Crudbooster\Controllers\DashboardController@index')->name('CbDashboard');
});

// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () {
    try {
        $args = request()->server('argv');
        if ($args && $args !== ['artisan', 'route:list']) {
            return ;
        }

        $modules = DB::table('cms_modules')->where('path', '!=', '')->where('controller', '!=', '')->where('is_protected', 0)->get();
        foreach ($modules as $module) {
            CbRouter::routeController($module->path, $module->controller);
        }
    } catch (Exception $e) {
       dd($e);
    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () {
    Route::post('{module}/do-upload-import-data', 'FileController@uploadImportData')->name('UploadImportData');
    Route::post('{module}/upload-summernote', 'FileController@uploadSummernote')->name('UploadImportData');
    Route::post('{module}/upload-file', 'FileController@uploadFile')->name('UploadImportData');
    Route::post('{module}/done-import', 'FileController@doneImport')->name('doneImportData');
});

