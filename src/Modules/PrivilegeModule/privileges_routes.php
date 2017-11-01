<?php
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\PrivilegeModule',
], function () {
    Route::get('privileges/', ['uses' => 'AdminPrivilegesController@getIndex', 'as' => 'AdminPrivilegesControllerGetIndex']);
    Route::get('privileges/add/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getAdd', 'as' => 'AdminPrivilegesControllerGetAdd']);
    Route::get('privileges/edit/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getEdit', 'as' => 'AdminPrivilegesControllerGetEdit']);
    Route::get('privileges/update-single/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getUpdateSingle', 'as' => 'AdminPrivilegesControllerGetUpdateSingle']);
    Route::get('privileges/detail/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDetail', 'as' => 'AdminPrivilegesControllerGetDetail']);
    Route::get('privileges/import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getImportData', 'as' => 'AdminPrivilegesControllerGetImportData']);
    Route::get('privileges/delete/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDelete', 'as' => 'AdminPrivilegesControllerGetDelete']);
    Route::get('privileges/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getExportData', 'as' => 'AdminPrivilegesControllerGetExportData']);
    Route::get('privileges/data-query/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDataQuery', 'as' => 'AdminPrivilegesControllerGetDataQuery']);
    Route::get('privileges/data-table/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDataTable', 'as' => 'AdminPrivilegesControllerGetDataTable']);
    Route::get('privileges/data-modal-datatable/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDataModalDatatable', 'as' => 'AdminPrivilegesControllerGetDataModalDatatable']);
    Route::get('privileges/delete-image/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@getDeleteImage', 'as' => 'AdminPrivilegesControllerGetDeleteImage']);
    Route::post('privileges/add-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postAddSave', 'as' => 'AdminPrivilegesControllerPostAddSave',]);
    Route::post('privileges/edit-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postEditSave', 'as' => 'AdminPrivilegesControllerPostEditSave',]);
    Route::post('privileges/done-import/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postDoneImport', 'as' => 'AdminPrivilegesControllerPostDoneImport',]);
    Route::post('privileges/do-import-chunk/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postDoImportChunk', 'as' => 'AdminPrivilegesControllerPostDoImportChunk',]);
    Route::post('privileges/do-upload-import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postDoUploadImportData', 'as' => 'AdminPrivilegesControllerPostDoUploadImportData',]);
    Route::post('privileges/action-selected/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postActionSelected', 'as' => 'AdminPrivilegesControllerPostActionSelected',]);
    Route::post('privileges/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postExportData', 'as' => 'AdminPrivilegesControllerPostExportData',]);
    Route::post('privileges/find-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postFindData', 'as' => 'AdminPrivilegesControllerPostFindData',]);
    Route::post('privileges/upload-summernote/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postUploadSummernote', 'as' => 'AdminPrivilegesControllerPostUploadSummernote',]);
    Route::post('privileges/upload-file/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postUploadFile', 'as' => 'AdminPrivilegesControllerPostUploadFile',]);
    Route::post('privileges/find-data-old/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminPrivilegesController@postFindDataOld', 'as' => 'AdminPrivilegesControllerPostFindDataOld',]);

});
