<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

$namespace = '\\crocodicstudio\\crudbooster\\SettingModule';

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('settings', 'AdminSettingsController', $namespace);
});