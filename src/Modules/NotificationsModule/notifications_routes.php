<?php

Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('NotificationsModule'),
], function () {
    Route::get('notifications/', 'AdminNotificationsController@getIndex')->name('AdminNotificationsControllerGetIndex');
    Route::get('notifications/latest-json', 'AdminNotificationsController@getLatestJson')->name('AdminNotificationsControllerGetLatestJson');
    Route::get('notifications/read/{id?}', 'AdminNotificationsController@getRead')->name('AdminNotificationsControllerGetRead');
    Route::get('notifications/data-query', 'AdminNotificationsController@getDataQuery')->name('AdminNotificationsControllerGetDataQuery');
    Route::get('notifications/update-single', 'AdminNotificationsController@getUpdateSingle')->name('AdminNotificationsControllerGetUpdateSingle');
    Route::get('notifications/add', 'AdminNotificationsController@getAdd')->name('AdminNotificationsControllerGetAdd');
    Route::get('notifications/edit/{id?}', 'AdminNotificationsController@getEdit')->name('AdminNotificationsControllerGetEdit');
    Route::get('notifications/delete/{id?}', 'AdminNotificationsController@getDelete')->name('AdminNotificationsControllerGetDelete');
    Route::get('notifications/detail/{id?}', 'AdminNotificationsController@getDetail')->name('AdminNotificationsControllerGetDetail');
});