<?php

$namespace = '\crocodicstudio\crudbooster\Modules\ModuleGenerator';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    Route::get('modules/', ['uses' => 'AdminModulesController@getIndex', 'as' => 'AdminModulesControllerGetIndex']);
    Route::get('modules/test', ['uses' => 'AdminModulesController@getTest', 'as' => 'AdminModulesControllerGetTest']);
    Route::get('modules/export-data', ['uses' => 'AdminModulesController@getExportData', 'as' => 'AdminModulesControllerGetExportData']);
    Route::get('modules/table-columns/{table?}', ['uses' => 'AdminModulesController@getTableColumns', 'as' => 'AdminModulesControllerGetTableColumns']);
    Route::get('modules/check-slug/{slug?}', ['uses' => 'AdminModulesController@getCheckSlug', 'as' => 'AdminModulesControllerGetCheckSlug']);
    Route::get('modules/add', ['uses' => 'AdminModulesController@getAdd', 'as' => 'AdminModulesControllerGetAdd']);

    Route::get('modules/step1/{id?}', ['uses' => 'AdminModulesController@getStep1', 'as' => 'AdminModulesControllerGetStep1']);
    Route::post('modules/step2', ['uses' => 'AdminModulesController@postStep2', 'as' => 'AdminModulesControllerPostStep2',]);

    Route::get('modules/step2/{id?}', ['uses' => 'AdminModulesController@getStep2', 'as' => 'AdminModulesControllerGetStep2']);
    Route::post('modules/step3', ['uses' => 'AdminModulesController@postStep3', 'as' => 'AdminModulesControllerPostStep3',]);

    Route::get('modules/step3/{id?}', ['uses' => 'AdminModulesController@getStep3', 'as' => 'AdminModulesControllerGetStep3']);
    Route::post('modules/step4', ['uses' => 'AdminModulesController@postStep4', 'as' => 'AdminModulesControllerPostStep4',]);

    Route::get('modules/type-info/{type?}', ['uses' => 'AdminModulesController@getTypeInfo', 'as' => 'AdminModulesControllerGetTypeInfo']);

    Route::get('modules/step4/{id?}', ['uses' => 'AdminModulesController@getStep4', 'as' => 'AdminModulesControllerGetStep4']);
    Route::post('modules/step-finish', ['uses' => 'AdminModulesController@postStepFinish', 'as' => 'AdminModulesControllerPostStepFinish',]);

    Route::get('modules/delete-image', ['uses' => 'AdminModulesController@getDeleteImage', 'as' => 'AdminModulesControllerGetDeleteImage']);
    Route::get('modules/data-query', ['uses' => 'AdminModulesController@getDataQuery', 'as' => 'AdminModulesControllerGetDataQuery']);
    Route::get('modules/data-table', ['uses' => 'AdminModulesController@getDataTable', 'as' => 'AdminModulesControllerGetDataTable']);
    Route::get('modules/data-modal-datatable', ['uses' => 'AdminModulesController@getDataModalDatatable', 'as' => 'AdminModulesControllerGetDataModalDatatable']);
    Route::get('modules/update-single', ['uses' => 'AdminModulesController@getUpdateSingle', 'as' => 'AdminModulesControllerGetUpdateSingle']);
    Route::get('modules/edit/{id?}', ['uses' => 'AdminModulesController@getEdit', 'as' => 'AdminModulesControllerGetEdit']);
    Route::get('modules/delete/{id?}', ['uses' => 'AdminModulesController@getDelete', 'as' => 'AdminModulesControllerGetDelete']);
    Route::get('modules/detail/{id?}', ['uses' => 'AdminModulesController@getDetail', 'as' => 'AdminModulesControllerGetDetail']);
    Route::get('modules/import-data', ['uses' => 'AdminModulesController@getImportData', 'as' => 'AdminModulesControllerGetImportData']);

    Route::post('modules/add-save', ['uses' => 'AdminModulesController@postAddSave', 'as' => 'AdminModulesControllerPostAddSave',]);
    Route::post('modules/edit-save/{one?}', ['uses' => 'AdminModulesController@postEditSave', 'as' => 'AdminModulesControllerPostEditSave',]);
    Route::post('modules/export-data', ['uses' => 'AdminModulesController@postExportData', 'as' => 'AdminModulesControllerPostExportData',]);
    Route::post('modules/find-data', ['uses' => 'AdminModulesController@postFindData', 'as' => 'AdminModulesControllerPostFindData',]);
    Route::post('modules/done-import', ['uses' => 'AdminModulesController@postDoneImport', 'as' => 'AdminModulesControllerPostDoneImport',]);
    Route::post('modules/do-import-chunk', ['uses' => 'AdminModulesController@postDoImportChunk', 'as' => 'AdminModulesControllerPostDoImportChunk',]);
    Route::post('modules/do-upload-import-data', ['uses' => 'AdminModulesController@postDoUploadImportData', 'as' => 'AdminModulesControllerPostDoUploadImportData',]);
    Route::post('modules/action-selected', ['uses' => 'AdminModulesController@postActionSelected', 'as' => 'AdminModulesControllerPostActionSelected',]);
    Route::post('modules/upload-summernote', ['uses' => 'AdminModulesController@postUploadSummernote', 'as' => 'AdminModulesControllerPostUploadSummernote',]);
    Route::post('modules/upload-file', ['uses' => 'AdminModulesController@postUploadFile', 'as' => 'AdminModulesControllerPostUploadFile',]);
});