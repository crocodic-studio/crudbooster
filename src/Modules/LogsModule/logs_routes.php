<?php
Route::get('logs/', ['uses' => 'AdminLogsController@getIndex', 'as' => 'AdminLogsControllerGetIndex']);
Route::get('logs/export-data', ['uses' => 'AdminLogsController@getExportData', 'as' => 'AdminLogsControllerGetExportData']);
Route::get('logs/data-query', ['uses' => 'AdminLogsController@getDataQuery', 'as' => 'AdminLogsControllerGetDataQuery']);
Route::get('logs/data-table', ['uses' => 'AdminLogsController@getDataTable', 'as' => 'AdminLogsControllerGetDataTable']);
Route::get('logs/data-modal-datatable/{one?}', ['uses' => 'AdminLogsController@getDataModalDatatable', 'as' => 'AdminLogsControllerGetDataModalDatatable']);
Route::get('logs/update-single/{one?}', ['uses' => 'AdminLogsController@getUpdateSingle', 'as' => 'AdminLogsControllerGetUpdateSingle']);
Route::get('logs/edit/{id?}', ['uses' => 'AdminLogsController@getEdit', 'as' => 'AdminLogsControllerGetEdit']);
Route::get('logs/add/{id?}', ['uses' => 'AdminLogsController@getAdd', 'as' => 'AdminLogsControllerGetAdd']);
Route::get('logs/delete/{id?}', ['uses' => 'AdminLogsController@getDelete', 'as' => 'AdminLogsControllerGetDelete']);
Route::get('logs/detail/{id?}', ['uses' => 'AdminLogsController@getDetail', 'as' => 'AdminLogsControllerGetDetail']);
Route::get('logs/import-data', ['uses' => 'AdminLogsController@getImportData', 'as' => 'AdminLogsControllerGetImportData']);


Route::post('logs/find-data-old/{one?}', ['uses' => 'AdminLogsController@postFindDataOld', 'as' => 'AdminLogsControllerPostFindDataOld',]);
Route::post('logs/find-data/{one?}', ['uses' => 'AdminLogsController@postFindData', 'as' => 'AdminLogsControllerPostFindData',]);
Route::post('logs/add-save/{one?}', [
    'uses' => 'AdminLogsController@postAddSave',
    'as' => 'AdminLogsControllerPostAddSave',
]);

Route::post('logs/do-import-chunk/{one?}', [
    'uses' => 'AdminLogsController@postDoImportChunk',
    'as' => 'AdminLogsControllerPostDoImportChunk',
]);

Route::post('logs/action-selected/{one?}', [
    'uses' => 'AdminLogsController@postActionSelected',
    'as' => 'AdminLogsControllerPostActionSelected',
]);

Route::post('logs/export-data/{one?}', [
    'uses' => 'AdminLogsController@postExportData',
    'as' => 'AdminLogsControllerPostExportData',
]);

Route::post('logs/edit-save/{one?}', [
    'uses' => 'AdminLogsController@postEditSave',
    'as' => 'AdminLogsControllerPostEditSave',
]);
