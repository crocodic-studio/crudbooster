<?php

$namespace = '\crocodicstudio\crudbooster\Modules\FileManagerModule';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    Route::get('file-manager/', ['uses' => 'AdminFileManagerController@getIndex', 'as' => 'AdminFileManagerControllerGetIndex']);

    Route::post('file-manager/create-directory/{one?}/{two?}/{three?}/{four?}/{five?}', [
        'uses' => 'AdminFileManagerController@postCreateDirectory',
        'as' => 'AdminFileManagerControllerPostCreateDirectory',
    ]);

    Route::post('file-manager/upload', [
        'uses' => 'AdminFileManagerController@postUpload',
        'as' => 'AdminFileManagerControllerPostUpload',
    ]);

    Route::get('file-manager/delete-directory/{one?}', [
        'uses' => 'AdminFileManagerController@getDeleteDirectory',
        'as' => 'AdminFileManagerControllerGetDeleteDirectory',
    ]);

    Route::get('file-manager/delete-file/{file}', [
        'uses' => 'AdminFileManagerController@getDeleteFile',
        'as' => 'AdminFileManagerControllerGetDeleteFile',
    ]);

    Route::get('file-manager/export-data', [
        'uses' => 'AdminFileManagerController@getExportData',
        'as' => 'AdminFileManagerControllerGetExportData',
    ]);

    Route::post('file-manager/export-data', [
        'uses' => 'AdminFileManagerController@postExportData',
        'as' => 'AdminFileManagerControllerPostExportData',
    ]);

    Route::get('file-manager/data-query', [
        'uses' => 'AdminFileManagerController@getDataQuery',
        'as' => 'AdminFileManagerControllerGetDataQuery',
    ]);

    Route::get('file-manager/data-table', [
        'uses' => 'AdminFileManagerController@getDataTable',
        'as' => 'AdminFileManagerControllerGetDataTable',
    ]);

    Route::get('file-manager/data-modal-datatable', [
        'uses' => 'AdminFileManagerController@getDataModalDatatable',
        'as' => 'AdminFileManagerControllerGetDataModalDatatable',
    ]);

    Route::get('file-manager/update-single', [
        'uses' => 'AdminFileManagerController@getUpdateSingle',
        'as' => 'AdminFileManagerControllerGetUpdateSingle',
    ]);

    Route::post('file-manager/find-data', [
        'uses' => 'AdminFileManagerController@postFindData',
        'as' => 'AdminFileManagerControllerPostFindData',
    ]);

    Route::get('file-manager/add', [
        'uses' => 'AdminFileManagerController@getAdd',
        'as' => 'AdminFileManagerControllerGetAdd',
    ]);

    Route::post('file-manager/add-save', [
        'uses' => 'AdminFileManagerController@postAddSave',
        'as' => 'AdminFileManagerControllerPostAddSave',
    ]);

    Route::get('file-manager/edit/{id?}', [
        'uses' => 'AdminFileManagerController@getEdit',
        'as' => 'AdminFileManagerControllerGetEdit',
    ]);

    Route::post('file-manager/edit-save/{id?}', [
        'uses' => 'AdminFileManagerController@postEditSave',
        'as' => 'AdminFileManagerControllerPostEditSave',
    ]);

    Route::get('file-manager/delete/{id?}', [
        'uses' => 'AdminFileManagerController@getDelete',
        'as' => 'AdminFileManagerControllerGetDelete',
    ]);

    Route::get('file-manager/detail/{id?}', [
        'uses' => 'AdminFileManagerController@getDetail',
        'as' => 'AdminFileManagerControllerGetDetail',
    ]);

    Route::get('file-manager/import-data', [
        'uses' => 'AdminFileManagerController@getImportData',
        'as' => 'AdminFileManagerControllerGetImportData',
    ]);

    Route::post('file-manager/done-import', [
        'uses' => 'AdminFileManagerController@postDoneImport',
        'as' => 'AdminFileManagerControllerPostDoneImport',
    ]);

    Route::post('file-manager/do-import-chunk', [
        'uses' => 'AdminFileManagerController@postDoImportChunk',
        'as' => 'AdminFileManagerControllerPostDoImportChunk',
    ]);

    Route::post('file-manager/do-upload-import-data', [
        'uses' => 'AdminFileManagerController@postDoUploadImportData',
        'as' => 'AdminFileManagerControllerPostDoUploadImportData',
    ]);

    Route::post('file-manager/action-selected', [
        'uses' => 'AdminFileManagerController@postActionSelected',
        'as' => 'AdminFileManagerControllerPostActionSelected',
    ]);

    Route::get('file-manager/delete-image', [
        'uses' => 'AdminFileManagerController@getDeleteImage',
        'as' => 'AdminFileManagerControllerGetDeleteImage',
    ]);

    Route::post('file-manager/upload-summernote', [
        'uses' => 'AdminFileManagerController@postUploadSummernote',
        'as' => 'AdminFileManagerControllerPostUploadSummernote',
    ]);

    Route::post('file-manager/upload-file', [
        'uses' => 'AdminFileManagerController@postUploadFile',
        'as' => 'AdminFileManagerControllerPostUploadFile',
    ]);

});