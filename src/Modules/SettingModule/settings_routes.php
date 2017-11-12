<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => '\\crocodicstudio\\crudbooster\\Modules\\SettingModule',
], function () {
    Route::get('settings/', ['uses' => 'AdminSettingsController@getIndex', 'as' => 'AdminSettingsControllerGetIndex']);
    Route::get('settings/show', ['uses' => 'AdminSettingsController@getShow', 'as' => 'AdminSettingsControllerGetShow']);
    Route::get('settings/delete-file-setting', ['uses' => 'AdminSettingsController@getDeleteFileSetting', 'as' => 'AdminSettingsControllerGetDeleteFileSetting']);
    Route::get('settings/export-data', ['uses' => 'AdminSettingsController@getExportData', 'as' => 'AdminSettingsControllerGetExportData']);
    Route::get('settings/data-query', ['uses' => 'AdminSettingsController@getDataQuery', 'as' => 'AdminSettingsControllerGetDataQuery']);
    Route::get('settings/data-table', ['uses' => 'AdminSettingsController@getDataTable', 'as' => 'AdminSettingsControllerGetDataTable']);
    Route::get('settings/data-modal-datatable', ['uses' => 'AdminSettingsController@getDataModalDatatable', 'as' => 'AdminSettingsControllerGetDataModalDatatable']);
    Route::get('settings/update-single', ['uses' => 'AdminSettingsController@getUpdateSingle', 'as' => 'AdminSettingsControllerGetUpdateSingle']);
    Route::get('settings/add', ['uses' => 'AdminSettingsController@getAdd', 'as' => 'AdminSettingsControllerGetAdd']);
    Route::get('settings/edit/{id?}', ['uses' => 'AdminSettingsController@getEdit', 'as' => 'AdminSettingsControllerGetEdit']);
    Route::get('settings/delete/{one?}', ['uses' => 'AdminSettingsController@getDelete', 'as' => 'AdminSettingsControllerGetDelete']);
    Route::get('settings/detail/{one?}', ['uses' => 'AdminSettingsController@getDetail', 'as' => 'AdminSettingsControllerGetDetail']);
    Route::get('settings/import-data', ['uses' => 'AdminSettingsController@getImportData', 'as' => 'AdminSettingsControllerGetImportData']);
    Route::get('settings/delete-image', ['uses' => 'AdminSettingsController@getDeleteImage', 'as' => 'AdminSettingsControllerGetDeleteImage']);

    Route::post('settings/done-import', ['uses' => 'AdminSettingsController@postDoneImport', 'as' => 'AdminSettingsControllerPostDoneImport',]);
    Route::post('settings/find-data', ['uses' => 'AdminSettingsController@postFindData', 'as' => 'AdminSettingsControllerPostFindData',]);
    Route::post('settings/save-setting', ['uses' => 'AdminSettingsController@postSaveSetting', 'as' => 'AdminSettingsControllerPostSaveSetting',]);
    Route::post('settings/export-data', ['uses' => 'AdminSettingsController@postExportData', 'as' => 'AdminSettingsControllerPostExportData',]);
    Route::post('settings/do-import-chunk', ['uses' => 'AdminSettingsController@postDoImportChunk', 'as' => 'AdminSettingsControllerPostDoImportChunk',]);
    Route::post('settings/do-upload-import-data', ['uses' => 'AdminSettingsController@postDoUploadImportData', 'as' => 'AdminSettingsControllerPostDoUploadImportData',]);
    Route::post('settings/action-selected', ['uses' => 'AdminSettingsController@postActionSelected', 'as' => 'AdminSettingsControllerPostActionSelected',]);
    Route::post('settings/add-save', ['uses' => 'AdminSettingsController@postAddSave', 'as' => 'AdminSettingsControllerPostAddSave',]);
    Route::post('settings/upload-summernote', ['uses' => 'AdminSettingsController@postUploadSummernote', 'as' => 'AdminSettingsControllerPostUploadSummernote',]);
    Route::post('settings/edit-save/{one?}', ['uses' => 'AdminSettingsController@postEditSave', 'as' => 'AdminSettingsControllerPostEditSave',]);
    Route::post('settings/upload-file', ['uses' => 'AdminSettingsController@postUploadFile', 'as' => 'AdminSettingsControllerPostUploadFile',]);
});