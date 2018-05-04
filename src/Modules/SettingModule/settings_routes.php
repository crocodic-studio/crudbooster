<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('SettingModule'),
], function () {
    Route::get('settings/', 'AdminSettingsController@getIndex')->name('AdminSettingsControllerGetIndex');
    Route::get('settings/show', 'AdminSettingsController@getShow')->name('AdminSettingsControllerGetShow');
    Route::get('settings/delete-file-setting', 'AdminSettingsController@getDeleteFileSetting')->name('AdminSettingsControllerGetDeleteFileSetting');
    Route::get('settings/export-data', 'AdminSettingsController@getExportData')->name('AdminSettingsControllerGetExportData');
    Route::get('settings/data-query', 'AdminSettingsController@getDataQuery')->name('AdminSettingsControllerGetDataQuery');
    Route::get('settings/data-table', 'AdminSettingsController@getDataTable')->name('AdminSettingsControllerGetDataTable');
    Route::get('settings/data-modal-datatable', 'AdminSettingsController@getDataModalDatatable')->name('AdminSettingsControllerGetDataModalDatatable');
    Route::get('settings/update-single', 'AdminSettingsController@getUpdateSingle')->name('AdminSettingsControllerGetUpdateSingle');
    Route::get('settings/add', 'AdminSettingsController@getAdd')->name('AdminSettingsControllerGetAdd');
    Route::get('settings/edit/{id?}', 'AdminSettingsController@getEdit')->name('AdminSettingsControllerGetEdit');
    Route::get('settings/delete/{one?}', 'AdminSettingsController@getDelete')->name('AdminSettingsControllerGetDelete');
    Route::get('settings/detail/{one?}', 'AdminSettingsController@getDetail')->name('AdminSettingsControllerGetDetail');
    Route::get('settings/import-data', 'AdminSettingsController@getImportData')->name('AdminSettingsControllerGetImportData');
    Route::get('settings/delete-image', 'AdminSettingsController@getDeleteImage')->name('AdminSettingsControllerGetDeleteImage');
    Route::post('settings/find-data', 'AdminSettingsController@postFindData')->name('AdminSettingsControllerPostFindData');
    Route::post('settings/save-setting', 'AdminSettingsController@postSaveSetting')->name('AdminSettingsControllerPostSaveSetting');
    Route::post('settings/export-data', 'AdminSettingsController@postExportData')->name('AdminSettingsControllerPostExportData');
    Route::post('settings/do-import-chunk', 'AdminSettingsController@postDoImportChunk')->name('AdminSettingsControllerPostDoImportChunk');
    Route::post('settings/action-selected', 'AdminSettingsController@postActionSelected')->name('AdminSettingsControllerPostActionSelected');
    Route::post('settings/add-save', 'AdminSettingsController@postAddSave')->name('AdminSettingsControllerPostAddSave');
    Route::post('settings/edit-save/{one?}', 'AdminSettingsController@postEditSave')->name('AdminSettingsControllerPostEditSave');
});