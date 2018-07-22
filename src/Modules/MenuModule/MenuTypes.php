<?php

namespace Crocodicstudio\Crudbooster\Modules\MenuModule;

class MenuTypes
{
    const Module = 'Module';

    const ControllerMethod = 'Controller & Method';

    const url = 'URL';

    const route = 'Route';

    public static function all()
    {
        return ['Module', 'Controller & Method', 'URL', 'Route'];
    }
}