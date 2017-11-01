<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\\crocodicstudio\\crudbooster\\Modules\\SettingModule',
], function () {
    Route::get('settings/', ['uses' => 'AdminSettingsController@getIndex', 'as' => 'AdminSettingsControllerGetIndex']);
    Route::get('settings/show/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getShow', 'as' => 'AdminSettingsControllerGetShow']);
    Route::get('settings/delete-file-setting/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDeleteFileSetting', 'as' => 'AdminSettingsControllerGetDeleteFileSetting']);
    Route::post('settings/save-setting/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postSaveSetting', 'as' => 'AdminSettingsControllerPostSaveSetting',]);
    Route::get('settings/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getExportData', 'as' => 'AdminSettingsControllerGetExportData']);
    Route::post('settings/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postExportData', 'as' => 'AdminSettingsControllerPostExportData',]);
    Route::get('settings/data-query/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDataQuery', 'as' => 'AdminSettingsControllerGetDataQuery']);
    Route::get('settings/data-table/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDataTable', 'as' => 'AdminSettingsControllerGetDataTable']);
    Route::get('settings/data-modal-datatable/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDataModalDatatable', 'as' => 'AdminSettingsControllerGetDataModalDatatable']);
    Route::get('settings/update-single/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getUpdateSingle', 'as' => 'AdminSettingsControllerGetUpdateSingle']);
    Route::post('settings/find-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postFindData', 'as' => 'AdminSettingsControllerPostFindData',]);
    Route::post('settings/find-data-old/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postFindDataOld', 'as' => 'AdminSettingsControllerPostFindDataOld',]);
    Route::get('settings/add/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getAdd', 'as' => 'AdminSettingsControllerGetAdd']);
    Route::post('settings/add-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postAddSave', 'as' => 'AdminSettingsControllerPostAddSave',]);
    Route::get('settings/edit/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getEdit', 'as' => 'AdminSettingsControllerGetEdit']);
    Route::post('settings/edit-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postEditSave', 'as' => 'AdminSettingsControllerPostEditSave',]);
    Route::get('settings/delete/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDelete', 'as' => 'AdminSettingsControllerGetDelete']);
    Route::get('settings/detail/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDetail', 'as' => 'AdminSettingsControllerGetDetail']);
    Route::get('settings/import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getImportData', 'as' => 'AdminSettingsControllerGetImportData']);
    Route::post('settings/done-import/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postDoneImport', 'as' => 'AdminSettingsControllerPostDoneImport',]);
    Route::post('settings/do-import-chunk/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postDoImportChunk', 'as' => 'AdminSettingsControllerPostDoImportChunk',]);
    Route::post('settings/do-upload-import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postDoUploadImportData', 'as' => 'AdminSettingsControllerPostDoUploadImportData',]);
    Route::post('settings/action-selected/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postActionSelected', 'as' => 'AdminSettingsControllerPostActionSelected',]);
    Route::get('settings/delete-image/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@getDeleteImage', 'as' => 'AdminSettingsControllerGetDeleteImage']);
    Route::post('settings/upload-summernote/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postUploadSummernote', 'as' => 'AdminSettingsControllerPostUploadSummernote',]);
    Route::post('settings/upload-file/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminSettingsController@postUploadFile', 'as' => 'AdminSettingsControllerPostUploadFile',]);
});