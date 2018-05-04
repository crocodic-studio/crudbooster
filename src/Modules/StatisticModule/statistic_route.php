<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('StatisticModule'),
], function () {
    Route::get('statistic-builder/', 'AdminStatisticBuilderController@getIndex')->name('AdminStatisticBuilderControllerGetIndex');
    Route::get('statistic-builder/show-dashboard', 'AdminStatisticBuilderController@getShowDashboard')->name('AdminStatisticBuilderControllerGetShowDashboard');
    Route::get('statistic-builder/show', 'AdminStatisticBuilderController@getShow')->name('AdminStatisticBuilderControllerGetShow');
    Route::get('statistic-builder/dashboard', 'AdminStatisticBuilderController@getDashboard')->name('AdminStatisticBuilderControllerGetDashboard');
    Route::get('statistic-builder/builder/{id_cms_statistics?}', 'AdminStatisticBuilderController@getBuilder')->name('AdminStatisticBuilderControllerGetBuilder');
    Route::get('statistic-builder/list-component/{one?}/{area_name?}', 'AdminStatisticBuilderController@getListComponent')->name('AdminStatisticBuilderControllerGetListComponent');
    Route::get('statistic-builder/view-component/{one?}', 'AdminStatisticBuilderController@getViewComponent')->name('AdminStatisticBuilderControllerGetViewComponent');
    Route::get('statistic-builder/edit-component/{one?}', 'AdminStatisticBuilderController@getEditComponent')->name('AdminStatisticBuilderControllerGetEditComponent');
    Route::get('statistic-builder/data-table', 'AdminStatisticBuilderController@getDataTable')->name('AdminStatisticBuilderControllerGetDataTable');
    Route::get('statistic-builder/data-modal-datatable', 'AdminStatisticBuilderController@getDataModalDatatable')->name('AdminStatisticBuilderControllerGetDataModalDatatable');
    Route::get('statistic-builder/update-single', 'AdminStatisticBuilderController@getUpdateSingle')->name('AdminStatisticBuilderControllerGetUpdateSingle');
    Route::get('statistic-builder/delete-component/{id?}', 'AdminStatisticBuilderController@getDeleteComponent')->name('AdminStatisticBuilderControllerGetDeleteComponent');
    Route::get('statistic-builder/data-query', 'AdminStatisticBuilderController@getDataQuery')->name('AdminStatisticBuilderControllerGetDataQuery');
    Route::get('statistic-builder/delete/{one?}', 'AdminStatisticBuilderController@getDelete')->name('AdminStatisticBuilderControllerGetDelete');
    Route::get('statistic-builder/detail/{one?}', 'AdminStatisticBuilderController@getDetail')->name('AdminStatisticBuilderControllerGetDetail');
    Route::get('statistic-builder/import-data', 'AdminStatisticBuilderController@getImportData')->name('AdminStatisticBuilderControllerGetImportData');
    Route::get('statistic-builder/export-data', 'AdminStatisticBuilderController@getExportData')->name('AdminStatisticBuilderControllerGetExportData');
    Route::get('statistic-builder/add', 'AdminStatisticBuilderController@getAdd')->name('AdminStatisticBuilderControllerGetAdd');
    Route::get('statistic-builder/delete-image', 'AdminStatisticBuilderController@getDeleteImage')->name('AdminStatisticBuilderControllerGetDeleteImage');
    Route::get('statistic-builder/edit/{one?}', 'AdminStatisticBuilderController@getEdit')->name('AdminStatisticBuilderControllerGetEdit');
    Route::post('statistic-builder/update-area-component', 'AdminStatisticBuilderController@postUpdateAreaComponent')->name('AdminStatisticBuilderControllerPostUpdateAreaComponent');
    Route::post('statistic-builder/save-component', 'AdminStatisticBuilderController@postSaveComponent')->name('AdminStatisticBuilderControllerPostSaveComponent');
    Route::post('statistic-builder/export-data', 'AdminStatisticBuilderController@postExportData')->name('AdminStatisticBuilderControllerPostExportData');
    Route::post('statistic-builder/add-component', 'AdminStatisticBuilderController@postAddComponent')->name('AdminStatisticBuilderControllerPostAddComponent');
    Route::post('statistic-builder/find-data', 'AdminStatisticBuilderController@postFindData')->name('AdminStatisticBuilderControllerPostFindData');
    Route::post('statistic-builder/add-save', 'AdminStatisticBuilderController@postAddSave')->name('AdminStatisticBuilderControllerPostAddSave');
    Route::post('statistic-builder/edit-save/{one?}', 'AdminStatisticBuilderController@postEditSave')->name('AdminStatisticBuilderControllerPostEditSave');
    Route::post('statistic-builder/do-import-chunk', 'AdminStatisticBuilderController@postDoImportChunk')->name('AdminStatisticBuilderControllerPostDoImportChunk');
    Route::post('statistic-builder/action-selected', 'AdminStatisticBuilderController@postActionSelected')->name('AdminStatisticBuilderControllerPostActionSelected');
});

