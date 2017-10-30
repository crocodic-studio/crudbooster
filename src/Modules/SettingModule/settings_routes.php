<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
], function () {
    CRUDBooster::routeController('settings', 'AdminSettingsController', '\\crocodicstudio\\crudbooster\\\Modules\\SettingModule');
});