<?php

Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('FileManagerModule'),
], function (){
    $ctrl = 'AdminFileManagerController';
    Route::get('file-manager/', $ctrl.'@getIndex')->name($ctrl.'GetIndex');

    Route::post('file-manager/create-directory/{one?}/{two?}/{three?}/{four?}/{five?}', $ctrl.'@postCreateDirectory')->name($ctrl.'PostCreateDirectory');
    Route::post('file-manager/upload', $ctrl.'@postUpload')->name($ctrl.'PostUpload');
    Route::get('file-manager/delete-directory/{one?}', $ctrl.'@getDeleteDirectory')->name($ctrl.'GetDeleteDirectory');
    Route::get('file-manager/delete-file/{file}', $ctrl.'@getDeleteFile')->name($ctrl.'GetDeleteFile');
    Route::get('file-manager/export-data', $ctrl.'@getExportData')->name($ctrl.'GetExportData');
    Route::post('file-manager/export-data', $ctrl.'@postExportData')->name($ctrl.'PostExportData');
    Route::get('file-manager/data-query', $ctrl.'@getDataQuery')->name($ctrl.'GetDataQuery');
    Route::get('file-manager/data-table', $ctrl.'@getDataTable')->name($ctrl.'GetDataTable');
    Route::get('file-manager/data-modal-datatable', $ctrl.'@getDataModalDatatable')->name($ctrl.'GetDataModalDatatable');
    Route::get('file-manager/update-single', $ctrl.'@getUpdateSingle')->name($ctrl.'GetUpdateSingle');
    Route::post('file-manager/find-data', $ctrl.'@postFindData')->name($ctrl.'PostFindData');
    Route::get('file-manager/add', $ctrl.'@getAdd')->name($ctrl.'GetAdd');
    Route::post('file-manager/add-save', $ctrl.'@postAddSave')->name($ctrl.'PostAddSave');
    Route::get('file-manager/edit/{id?}', $ctrl.'@getEdit')->name($ctrl.'GetEdit');
    Route::post('file-manager/edit-save/{id?}', $ctrl.'@postEditSave')->name($ctrl.'PostEditSave');
    Route::get('file-manager/delete/{id?}', $ctrl.'@getDelete')->name($ctrl.'GetDelete');
    Route::get('file-manager/detail/{id?}', $ctrl.'@getDetail')->name($ctrl.'GetDetail');
    Route::get('file-manager/import-data', $ctrl.'@getImportData')->name($ctrl.'GetImportData');
    Route::post('file-manager/done-import', $ctrl.'@postDoneImport')->name($ctrl.'PostDoneImport');
    Route::post('file-manager/do-import-chunk', $ctrl.'@postDoImportChunk')->name($ctrl.'PostDoImportChunk');
    Route::post('file-manager/do-upload-import-data', $ctrl.'@postDoUploadImportData')->name($ctrl.'PostDoUploadImportData');
    Route::post('file-manager/action-selected', $ctrl.'@postActionSelected')->name($ctrl.'PostActionSelected');
    Route::get('file-manager/delete-image', $ctrl.'@getDeleteImage')->name($ctrl.'GetDeleteImage');
    //Route::post('file-manager/upload-summernote', 'AdminFileManagerController@postUploadSummernote')->name('AdminFileManagerControllerPostUploadSummernote');
    //Route::post('file-manager/upload-file', 'AdminFileManagerController@postUploadFile')->name('AdminFileManagerControllerPostUploadFile');
});