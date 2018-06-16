<?php

$namespace = cbModulesNS('ModuleGenerator');

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    Route::get('modules/', 'AdminModulesController@getIndex')->name('AdminModulesControllerGetIndex');
    Route::get('modules/table-columns/{table?}', 'AdminModulesController@getTableColumns')->name('AdminModulesControllerGetTableColumns');
    Route::get('modules/check-slug/{slug?}', 'AdminModulesController@getCheckSlug')->name('AdminModulesControllerGetCheckSlug');
    Route::get('modules/add', 'AdminModulesController@getAdd')->name('AdminModulesControllerGetAdd');

    Route::get('modules/step1/{id?}', 'AdminModulesController@getStep1')->name('AdminModulesControllerGetStep1');
    Route::post('modules/step1', 'AdminModulesController@postStep1')->name('AdminModulesControllerPostStep1');

    Route::get('modules/step2/{id?}', 'AdminModulesController@getStep2')->name('AdminModulesControllerGetStep2');
    Route::post('modules/step3', 'AdminModulesController@postStep2')->name('AdminModulesControllerPostStep2');

    Route::get('modules/step3/{id?}', 'AdminModulesController@getStep3')->name('AdminModulesControllerGetStep3');
    Route::post('modules/step4', 'AdminModulesController@postStep3')->name('AdminModulesControllerPostStep3');

    Route::get('modules/type-info/{type?}', 'AdminModulesController@getTypeInfo')->name('AdminModulesControllerGetTypeInfo');

    Route::get('modules/step4/{id?}', 'AdminModulesController@getStep4')->name('AdminModulesControllerGetStep4');
    Route::post('modules/step-finish', 'AdminModulesController@postStepFinish')->name('AdminModulesControllerPostStepFinish');

    Route::get('modules/delete-image', 'AdminModulesController@getDeleteImage')->name('AdminModulesControllerGetDeleteImage');
    Route::get('modules/data-query', 'AdminModulesController@getDataQuery')->name('AdminModulesControllerGetDataQuery');
    Route::get('modules/data-table', 'AdminModulesController@getDataTable')->name('AdminModulesControllerGetDataTable');
    Route::get('modules/data-modal-datatable', 'AdminModulesController@getDataModalDatatable')->name('AdminModulesControllerGetDataModalDatatable');
    Route::get('modules/update-single', 'AdminModulesController@getUpdateSingle')->name('AdminModulesControllerGetUpdateSingle');
    Route::get('modules/edit/{id?}', 'AdminModulesController@getEdit')->name('AdminModulesControllerGetEdit');
    Route::get('modules/delete/{id?}', 'AdminModulesController@getDelete')->name('AdminModulesControllerGetDelete');
    Route::get('modules/detail/{id?}', 'AdminModulesController@getDetail')->name('AdminModulesControllerGetDetail');

    Route::post('modules/add-save', 'AdminModulesController@postAddSave')->name('AdminModulesControllerPostAddSave');
    Route::post('modules/edit-save/{one?}', 'AdminModulesController@postEditSave')->name('AdminModulesControllerPostEditSave');
    Route::post('modules/find-data', 'AdminModulesController@postFindData')->name('AdminModulesControllerPostFindData');
    Route::post('modules/action-selected', 'AdminModulesController@postActionSelected')->name('AdminModulesControllerPostActionSelected');
});
