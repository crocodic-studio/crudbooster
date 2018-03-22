<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

class MenuTypes
{
    const Module = 'Module';

    const ControllerMethod = 'Controller & Method';

    const url = 'URL';

    const Statistic = 'Statistic';

    const route = 'Route';

    public static function all()
    {
        return ['Module', 'Controller & Method', 'URL', 'Statistic', 'Route'];
    }
}