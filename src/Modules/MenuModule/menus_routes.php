<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\MenuModule',
], function () {
    Route::get('menus/', ['uses' => 'AdminMenusController@getIndex', 'as' => 'AdminMenusControllerGetIndex']);
    Route::post('menus/save-menu/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postSaveMenu', 'as' => 'AdminMenusControllerPostSaveMenu',]);
    Route::get('menus/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getExportData', 'as' => 'AdminMenusControllerGetExportData']);
    Route::post('menus/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postExportData', 'as' => 'AdminMenusControllerPostExportData',]);
    Route::get('menus/data-query/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDataQuery', 'as' => 'AdminMenusControllerGetDataQuery']);
    Route::get('menus/data-table/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDataTable', 'as' => 'AdminMenusControllerGetDataTable']);
    Route::get('menus/data-modal-datatable/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDataModalDatatable', 'as' => 'AdminMenusControllerGetDataModalDatatable']);
    Route::get('menus/update-single/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getUpdateSingle', 'as' => 'AdminMenusControllerGetUpdateSingle']);
    Route::post('menus/find-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postFindData', 'as' => 'AdminMenusControllerPostFindData',]);
    Route::post('menus/find-data-old/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postFindDataOld', 'as' => 'AdminMenusControllerPostFindDataOld',]);
    Route::get('menus/add/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getAdd', 'as' => 'AdminMenusControllerGetAdd']);
    Route::post('menus/add-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postAddSave', 'as' => 'AdminMenusControllerPostAddSave',]);
    Route::get('menus/edit/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getEdit', 'as' => 'AdminMenusControllerGetEdit']);
    Route::post('menus/edit-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postEditSave', 'as' => 'AdminMenusControllerPostEditSave',]);
    Route::get('menus/delete/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDelete', 'as' => 'AdminMenusControllerGetDelete']);
    Route::get('menus/detail/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDetail', 'as' => 'AdminMenusControllerGetDetail']);
    Route::get('menus/import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getImportData', 'as' => 'AdminMenusControllerGetImportData']);
    Route::post('menus/done-import/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postDoneImport', 'as' => 'AdminMenusControllerPostDoneImport',]);
    Route::post('menus/do-import-chunk/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postDoImportChunk', 'as' => 'AdminMenusControllerPostDoImportChunk',]);
    Route::post('menus/do-upload-import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postDoUploadImportData', 'as' => 'AdminMenusControllerPostDoUploadImportData',]);
    Route::post('menus/action-selected/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postActionSelected', 'as' => 'AdminMenusControllerPostActionSelected',]);
    Route::get('menus/delete-image/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@getDeleteImage', 'as' => 'AdminMenusControllerGetDeleteImage']);
    Route::post('menus/upload-summernote/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postUploadSummernote', 'as' => 'AdminMenusControllerPostUploadSummernote',]);
    Route::post('menus/upload-file/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminMenusController@postUploadFile', 'as' => 'AdminMenusControllerPostUploadFile',]);
});

