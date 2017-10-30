<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\StatisticModule',
], function () {
    CRUDBooster::routeController('statistic-builder', 'AdminStatisticBuilderController', '\crocodicstudio\crudbooster\Modules\StatisticModule');
});

