<?php

$namespace = '\crocodicstudio\crudbooster\Modules\ModuleGenerator';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    Route::get('modules/', ['uses' => 'AdminModulesController@getIndex', 'as' => 'AdminModulesControllerGetIndex']);
    Route::get('modules/table-columns/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getTableColumns', 'as' => 'AdminModulesControllerGetTableColumns']);
    Route::get('modules/check-slug/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getCheckSlug', 'as' => 'AdminModulesControllerGetCheckSlug']);
    Route::get('modules/add/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getAdd', 'as' => 'AdminModulesControllerGetAdd']);
    Route::get('modules/step1/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getStep1', 'as' => 'AdminModulesControllerGetStep1']);
    Route::get('modules/step2/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getStep2', 'as' => 'AdminModulesControllerGetStep2']);
    Route::post('modules/step2/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postStep2', 'as' => 'AdminModulesControllerPostStep2',]);
    Route::post('modules/step3/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postStep3', 'as' => 'AdminModulesControllerPostStep3',]);
    Route::get('modules/step3/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getStep3', 'as' => 'AdminModulesControllerGetStep3']);
    Route::get('modules/type-info/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getTypeInfo', 'as' => 'AdminModulesControllerGetTypeInfo']);
    Route::post('modules/step4/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postStep4', 'as' => 'AdminModulesControllerPostStep4',]);
    Route::get('modules/step4/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getStep4', 'as' => 'AdminModulesControllerGetStep4']);
    Route::post('modules/step-finish/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postStepFinish', 'as' => 'AdminModulesControllerPostStepFinish',]);
    Route::post('modules/add-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postAddSave', 'as' => 'AdminModulesControllerPostAddSave',]);
    Route::post('modules/edit-save/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postEditSave', 'as' => 'AdminModulesControllerPostEditSave',]);
    Route::get('modules/test/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getTest', 'as' => 'AdminModulesControllerGetTest']);
    Route::get('modules/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getExportData', 'as' => 'AdminModulesControllerGetExportData']);
    Route::post('modules/export-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postExportData', 'as' => 'AdminModulesControllerPostExportData',]);
    Route::get('modules/data-query/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDataQuery', 'as' => 'AdminModulesControllerGetDataQuery']);
    Route::get('modules/data-table/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDataTable', 'as' => 'AdminModulesControllerGetDataTable']);
    Route::get('modules/data-modal-datatable/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDataModalDatatable', 'as' => 'AdminModulesControllerGetDataModalDatatable']);
    Route::get('modules/update-single/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getUpdateSingle', 'as' => 'AdminModulesControllerGetUpdateSingle']);
    Route::post('modules/find-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postFindData', 'as' => 'AdminModulesControllerPostFindData',]);
    Route::post('modules/find-data-old/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postFindDataOld', 'as' => 'AdminModulesControllerPostFindDataOld',]);
    Route::get('modules/edit/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getEdit', 'as' => 'AdminModulesControllerGetEdit']);
    Route::get('modules/delete/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDelete', 'as' => 'AdminModulesControllerGetDelete']);
    Route::get('modules/detail/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDetail', 'as' => 'AdminModulesControllerGetDetail']);
    Route::get('modules/import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getImportData', 'as' => 'AdminModulesControllerGetImportData']);
    Route::post('modules/done-import/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postDoneImport', 'as' => 'AdminModulesControllerPostDoneImport',]);
    Route::post('modules/do-import-chunk/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postDoImportChunk', 'as' => 'AdminModulesControllerPostDoImportChunk',]);
    Route::post('modules/do-upload-import-data/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postDoUploadImportData', 'as' => 'AdminModulesControllerPostDoUploadImportData',]);
    Route::post('modules/action-selected/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postActionSelected', 'as' => 'AdminModulesControllerPostActionSelected',]);
    Route::get('modules/delete-image/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@getDeleteImage', 'as' => 'AdminModulesControllerGetDeleteImage']);
    Route::post('modules/upload-summernote/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postUploadSummernote', 'as' => 'AdminModulesControllerPostUploadSummernote',]);
    Route::post('modules/upload-file/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'AdminModulesController@postUploadFile', 'as' => 'AdminModulesControllerPostUploadFile',]);
});