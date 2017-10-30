<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
], function () {
    CRUDBooster::routeController('menus', 'AdminMenusController', '\crocodicstudio\crudbooster\Modules\MenuModule');
});

