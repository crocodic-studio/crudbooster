<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => '\crocodicstudio\crudbooster\Modules\StatisticModule',
], function () {
    Route::get('statistic-builder/', ['uses' => 'AdminStatisticBuilderController@getIndex', 'as' => 'AdminStatisticBuilderControllerGetIndex']);
    Route::get('statistic-builder/show-dashboard', ['uses' => 'AdminStatisticBuilderController@getShowDashboard', 'as' => 'AdminStatisticBuilderControllerGetShowDashboard']);
    Route::get('statistic-builder/show', ['uses' => 'AdminStatisticBuilderController@getShow', 'as' => 'AdminStatisticBuilderControllerGetShow']);
    Route::get('statistic-builder/dashboard', ['uses' => 'AdminStatisticBuilderController@getDashboard', 'as' => 'AdminStatisticBuilderControllerGetDashboard']);
    Route::get('statistic-builder/builder/{id_cms_statistics?}', ['uses' => 'AdminStatisticBuilderController@getBuilder', 'as' => 'AdminStatisticBuilderControllerGetBuilder']);
    Route::get('statistic-builder/list-component/{one?}/{area_name?}', ['uses' => 'AdminStatisticBuilderController@getListComponent', 'as' => 'AdminStatisticBuilderControllerGetListComponent']);
    Route::get('statistic-builder/view-component/{one?}', ['uses' => 'AdminStatisticBuilderController@getViewComponent', 'as' => 'AdminStatisticBuilderControllerGetViewComponent']);
    Route::get('statistic-builder/edit-component/{one?}', ['uses' => 'AdminStatisticBuilderController@getEditComponent', 'as' => 'AdminStatisticBuilderControllerGetEditComponent']);
    Route::get('statistic-builder/data-table', ['uses' => 'AdminStatisticBuilderController@getDataTable', 'as' => 'AdminStatisticBuilderControllerGetDataTable']);
    Route::get('statistic-builder/data-modal-datatable', ['uses' => 'AdminStatisticBuilderController@getDataModalDatatable', 'as' => 'AdminStatisticBuilderControllerGetDataModalDatatable']);
    Route::get('statistic-builder/update-single', ['uses' => 'AdminStatisticBuilderController@getUpdateSingle', 'as' => 'AdminStatisticBuilderControllerGetUpdateSingle']);
    Route::get('statistic-builder/delete-component/{id?}', ['uses' => 'AdminStatisticBuilderController@getDeleteComponent', 'as' => 'AdminStatisticBuilderControllerGetDeleteComponent']);
    Route::get('statistic-builder/data-query', ['uses' => 'AdminStatisticBuilderController@getDataQuery', 'as' => 'AdminStatisticBuilderControllerGetDataQuery']);
    Route::get('statistic-builder/delete/{one?}', ['uses' => 'AdminStatisticBuilderController@getDelete', 'as' => 'AdminStatisticBuilderControllerGetDelete']);
    Route::get('statistic-builder/detail/{one?}', ['uses' => 'AdminStatisticBuilderController@getDetail', 'as' => 'AdminStatisticBuilderControllerGetDetail']);
    Route::get('statistic-builder/import-data', ['uses' => 'AdminStatisticBuilderController@getImportData', 'as' => 'AdminStatisticBuilderControllerGetImportData']);
    Route::get('statistic-builder/export-data', ['uses' => 'AdminStatisticBuilderController@getExportData', 'as' => 'AdminStatisticBuilderControllerGetExportData']);
    Route::get('statistic-builder/add', ['uses' => 'AdminStatisticBuilderController@getAdd', 'as' => 'AdminStatisticBuilderControllerGetAdd']);
    Route::get('statistic-builder/delete-image', ['uses' => 'AdminStatisticBuilderController@getDeleteImage', 'as' => 'AdminStatisticBuilderControllerGetDeleteImage']);
    Route::get('statistic-builder/edit/{one?}', ['uses' => 'AdminStatisticBuilderController@getEdit', 'as' => 'AdminStatisticBuilderControllerGetEdit']);

    Route::post('statistic-builder/update-area-component', ['uses' => 'AdminStatisticBuilderController@postUpdateAreaComponent', 'as' => 'AdminStatisticBuilderControllerPostUpdateAreaComponent',]);
    Route::post('statistic-builder/save-component', ['uses' => 'AdminStatisticBuilderController@postSaveComponent', 'as' => 'AdminStatisticBuilderControllerPostSaveComponent',]);
    Route::post('statistic-builder/export-data', ['uses' => 'AdminStatisticBuilderController@postExportData', 'as' => 'AdminStatisticBuilderControllerPostExportData',]);
    Route::post('statistic-builder/add-component', ['uses' => 'AdminStatisticBuilderController@postAddComponent', 'as' => 'AdminStatisticBuilderControllerPostAddComponent',]);
    Route::post('statistic-builder/find-data', ['uses' => 'AdminStatisticBuilderController@postFindData', 'as' => 'AdminStatisticBuilderControllerPostFindData',]);
    Route::post('statistic-builder/add-save', ['uses' => 'AdminStatisticBuilderController@postAddSave', 'as' => 'AdminStatisticBuilderControllerPostAddSave',]);
    Route::post('statistic-builder/edit-save/{one?}', ['uses' => 'AdminStatisticBuilderController@postEditSave', 'as' => 'AdminStatisticBuilderControllerPostEditSave',]);
    Route::post('statistic-builder/done-import', ['uses' => 'AdminStatisticBuilderController@postDoneImport', 'as' => 'AdminStatisticBuilderControllerPostDoneImport',]);
    Route::post('statistic-builder/do-import-chunk', ['uses' => 'AdminStatisticBuilderController@postDoImportChunk', 'as' => 'AdminStatisticBuilderControllerPostDoImportChunk',]);
    Route::post('statistic-builder/do-upload-import-data', ['uses' => 'AdminStatisticBuilderController@postDoUploadImportData', 'as' => 'AdminStatisticBuilderControllerPostDoUploadImportData',]);
    Route::post('statistic-builder/action-selected', ['uses' => 'AdminStatisticBuilderController@postActionSelected', 'as' => 'AdminStatisticBuilderControllerPostActionSelected',]);
    Route::post('statistic-builder/upload-summernote', ['uses' => 'AdminStatisticBuilderController@postUploadSummernote', 'as' => 'AdminStatisticBuilderControllerPostUploadSummernote',]);
    Route::post('statistic-builder/upload-file', ['uses' => 'AdminStatisticBuilderController@postUploadFile', 'as' => 'AdminStatisticBuilderControllerPostUploadFile',]);

});

