<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\MenuModule',
], function () {
    Route::get('menus/', ['uses' => 'AdminMenusController@getIndex', 'as' => 'AdminMenusControllerGetIndex']);
    Route::get('menus/export-data', ['uses' => 'AdminMenusController@getExportData', 'as' => 'AdminMenusControllerGetExportData']);
    Route::get('menus/data-query', ['uses' => 'AdminMenusController@getDataQuery', 'as' => 'AdminMenusControllerGetDataQuery']);
    Route::get('menus/data-table', ['uses' => 'AdminMenusController@getDataTable', 'as' => 'AdminMenusControllerGetDataTable']);
    Route::get('menus/data-modal-datatable', ['uses' => 'AdminMenusController@getDataModalDatatable', 'as' => 'AdminMenusControllerGetDataModalDatatable']);
    Route::get('menus/update-single', ['uses' => 'AdminMenusController@getUpdateSingle', 'as' => 'AdminMenusControllerGetUpdateSingle']);
    Route::get('menus/add', ['uses' => 'AdminMenusController@getAdd', 'as' => 'AdminMenusControllerGetAdd']);
    Route::get('menus/edit/{id?}', ['uses' => 'AdminMenusController@getEdit', 'as' => 'AdminMenusControllerGetEdit']);
    Route::get('menus/delete/{id?}', ['uses' => 'AdminMenusController@getDelete', 'as' => 'AdminMenusControllerGetDelete']);
    Route::get('menus/detail/{id?}', ['uses' => 'AdminMenusController@getDetail', 'as' => 'AdminMenusControllerGetDetail']);
    Route::get('menus/import-data', ['uses' => 'AdminMenusController@getImportData', 'as' => 'AdminMenusControllerGetImportData']);
    Route::get('menus/delete-image', ['uses' => 'AdminMenusController@getDeleteImage', 'as' => 'AdminMenusControllerGetDeleteImage']);

    Route::post('menus/save-menu', ['uses' => 'AdminMenusController@postSaveMenu', 'as' => 'AdminMenusControllerPostSaveMenu',]);
    Route::post('menus/export-data', ['uses' => 'AdminMenusController@postExportData', 'as' => 'AdminMenusControllerPostExportData',]);
    Route::post('menus/find-data-old', ['uses' => 'AdminMenusController@postFindDataOld', 'as' => 'AdminMenusControllerPostFindDataOld',]);
    Route::post('menus/add-save', ['uses' => 'AdminMenusController@postAddSave', 'as' => 'AdminMenusControllerPostAddSave',]);
    Route::post('menus/edit-save/{id?}', ['uses' => 'AdminMenusController@postEditSave', 'as' => 'AdminMenusControllerPostEditSave',]);
    Route::post('menus/find-data', ['uses' => 'AdminMenusController@postFindData', 'as' => 'AdminMenusControllerPostFindData',]);
    Route::post('menus/done-import', ['uses' => 'AdminMenusController@postDoneImport', 'as' => 'AdminMenusControllerPostDoneImport',]);
    Route::post('menus/do-import-chunk', ['uses' => 'AdminMenusController@postDoImportChunk', 'as' => 'AdminMenusControllerPostDoImportChunk',]);
    Route::post('menus/do-upload-import-data', ['uses' => 'AdminMenusController@postDoUploadImportData', 'as' => 'AdminMenusControllerPostDoUploadImportData',]);
    Route::post('menus/action-selected', ['uses' => 'AdminMenusController@postActionSelected', 'as' => 'AdminMenusControllerPostActionSelected',]);
    Route::post('menus/upload-summernote', ['uses' => 'AdminMenusController@postUploadSummernote', 'as' => 'AdminMenusControllerPostUploadSummernote',]);
    Route::post('menus/upload-file', ['uses' => 'AdminMenusController@postUploadFile', 'as' => 'AdminMenusControllerPostUploadFile',]);
});

