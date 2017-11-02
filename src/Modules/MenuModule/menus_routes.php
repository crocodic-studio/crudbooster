<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\MenuModule',
], function () {
    Route::get('menus/', ['uses' => 'AdminMenusController@getIndex', 'as' => 'AdminMenusControllerGetIndex']);
    Route::get('menus/export-data', ['uses' => 'AdminMenusController@getExportData', 'as' => 'AdminMenusControllerGetExportData']);
    Route::get('menus/data-query/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDataQuery', 'as' => 'AdminMenusControllerGetDataQuery']);
    Route::get('menus/data-table', ['uses' => 'AdminMenusController@getDataTable', 'as' => 'AdminMenusControllerGetDataTable']);
    Route::get('menus/data-modal-datatable/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDataModalDatatable', 'as' => 'AdminMenusControllerGetDataModalDatatable']);
    Route::get('menus/update-single', ['uses' => 'AdminMenusController@getUpdateSingle', 'as' => 'AdminMenusControllerGetUpdateSingle']);
    Route::get('menus/add/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getAdd', 'as' => 'AdminMenusControllerGetAdd']);
    Route::get('menus/edit/{one?}', ['uses' => 'AdminMenusController@getEdit', 'as' => 'AdminMenusControllerGetEdit']);
    Route::get('menus/delete/{id?}', ['uses' => 'AdminMenusController@getDelete', 'as' => 'AdminMenusControllerGetDelete']);
    Route::get('menus/detail/{one?}', ['uses' => 'AdminMenusController@getDetail', 'as' => 'AdminMenusControllerGetDetail']);
    Route::get('menus/import-data', ['uses' => 'AdminMenusController@getImportData', 'as' => 'AdminMenusControllerGetImportData']);
    Route::get('menus/delete-image', ['uses' => 'AdminMenusController@getDeleteImage', 'as' => 'AdminMenusControllerGetDeleteImage']);

    Route::post('menus/save-menu/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postSaveMenu', 'as' => 'AdminMenusControllerPostSaveMenu',]);
    Route::post('menus/export-data', ['uses' => 'AdminMenusController@postExportData', 'as' => 'AdminMenusControllerPostExportData',]);
    Route::post('menus/find-data-old/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postFindDataOld', 'as' => 'AdminMenusControllerPostFindDataOld',]);
    Route::post('menus/add-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postAddSave', 'as' => 'AdminMenusControllerPostAddSave',]);
    Route::post('menus/edit-save/{one?}', ['uses' => 'AdminMenusController@postEditSave', 'as' => 'AdminMenusControllerPostEditSave',]);
    Route::post('menus/find-data', ['uses' => 'AdminMenusController@postFindData', 'as' => 'AdminMenusControllerPostFindData',]);
    Route::post('menus/done-import', ['uses' => 'AdminMenusController@postDoneImport', 'as' => 'AdminMenusControllerPostDoneImport',]);
    Route::post('menus/do-import-chunk', ['uses' => 'AdminMenusController@postDoImportChunk', 'as' => 'AdminMenusControllerPostDoImportChunk',]);
    Route::post('menus/do-upload-import-data', ['uses' => 'AdminMenusController@postDoUploadImportData', 'as' => 'AdminMenusControllerPostDoUploadImportData',]);
    Route::post('menus/action-selected/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postActionSelected', 'as' => 'AdminMenusControllerPostActionSelected',]);
    Route::post('menus/upload-summernote/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postUploadSummernote', 'as' => 'AdminMenusControllerPostUploadSummernote',]);
    Route::post('menus/upload-file/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postUploadFile', 'as' => 'AdminMenusControllerPostUploadFile',]);
});

