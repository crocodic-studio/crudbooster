<?php

$namespace = '\crocodicstudio\crudbooster\Modules\FileManagerModule';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('file-manager', 'AdminFileManagerController', $namespace);
});