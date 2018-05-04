<?php
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('PrivilegeModule'),
], function () {
    Route::get('privileges/', 'AdminPrivilegesController@getIndex')->name('AdminPrivilegesControllerGetIndex');
    Route::get('privileges/add', 'AdminPrivilegesController@getAdd')->name('AdminPrivilegesControllerGetAdd');
    Route::get('privileges/edit/{id?}', 'AdminPrivilegesController@getEdit')->name('AdminPrivilegesControllerGetEdit');
    Route::get('privileges/update-single', 'AdminPrivilegesController@getUpdateSingle')->name('AdminPrivilegesControllerGetUpdateSingle');
    Route::get('privileges/detail/{id?}', 'AdminPrivilegesController@getDetail')->name('AdminPrivilegesControllerGetDetail');
    Route::get('privileges/import-data', 'AdminPrivilegesController@getImportData')->name('AdminPrivilegesControllerGetImportData');
    Route::get('privileges/delete/{id?}', 'AdminPrivilegesController@getDelete')->name('AdminPrivilegesControllerGetDelete');
    Route::get('privileges/export-data', 'AdminPrivilegesController@getExportData')->name('AdminPrivilegesControllerGetExportData');
    Route::get('privileges/data-query', 'AdminPrivilegesController@getDataQuery')->name('AdminPrivilegesControllerGetDataQuery');
    Route::get('privileges/data-table', 'AdminPrivilegesController@getDataTable')->name('AdminPrivilegesControllerGetDataTable');
    Route::get('privileges/data-modal-datatable', 'AdminPrivilegesController@getDataModalDatatable')->name('AdminPrivilegesControllerGetDataModalDatatable');
    Route::get('privileges/delete-image', 'AdminPrivilegesController@getDeleteImage')->name('AdminPrivilegesControllerGetDeleteImage');
    Route::post('privileges/add-save', 'AdminPrivilegesController@postAddSave')->name('AdminPrivilegesControllerPostAddSave');
    Route::post('privileges/edit-save/{id?}', 'AdminPrivilegesController@postEditSave')->name('AdminPrivilegesControllerPostEditSave');
    Route::post('privileges/do-import-chunk', 'AdminPrivilegesController@postDoImportChunk')->name('AdminPrivilegesControllerPostDoImportChunk');
    Route::post('privileges/action-selected', 'AdminPrivilegesController@postActionSelected')->name('AdminPrivilegesControllerPostActionSelected');
    Route::post('privileges/export-data', 'AdminPrivilegesController@postExportData')->name('AdminPrivilegesControllerPostExportData');
    Route::post('privileges/find-data', 'AdminPrivilegesController@postFindData')->name('AdminPrivilegesControllerPostFindData');

    //Route::post('privileges/do-upload-import-data', ['uses' => 'AdminPrivilegesController@postDoUploadImportData', 'as' => 'AdminPrivilegesControllerPostDoUploadImportData',]);
    //Route::post('privileges/done-import', ['uses' => 'AdminPrivilegesController@postDoneImport', 'as' => 'AdminPrivilegesControllerPostDoneImport',]);
    //Route::post('privileges/upload-summernote', ['uses' => 'AdminPrivilegesController@postUploadSummernote', 'as' => 'AdminPrivilegesControllerPostUploadSummernote',]);
    //Route::post('privileges/upload-file', ['uses' => 'AdminPrivilegesController@postUploadFile', 'as' => 'AdminPrivilegesControllerPostUploadFile',]);

});
