<?php

$namespace = '\crocodicstudio\crudbooster\Modules\ModuleGenerator';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('modules', 'AdminModulesController', $namespace);
});