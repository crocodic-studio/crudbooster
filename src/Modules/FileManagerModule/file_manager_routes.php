<?php

$namespace = '\crocodicstudio\crudbooster\Modules\FileManagerModule';

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('file-manager', 'AdminFileManagerController', $namespace);
});