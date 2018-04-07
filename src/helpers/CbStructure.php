<?php

namespace crocodicstudio\crudbooster\helpers;

class CbStructure
{
    public static function componentsPath($type = '')
    {
        $componentPath = implode(DIRECTORY_SEPARATOR, ['vendor', 'crocodicstudio', 'crudbooster', 'src', 'views', 'form', 'type_components', $type]);

        return base_path($componentPath);
    }

    public static function publishedComponentsPath($type = '')
    {
        $Path = implode(DIRECTORY_SEPARATOR, ['views', 'vendor', 'crudbooster', 'type_components', $type]);

        return resource_path($Path);
    }

    public static function controllersDir()
    {
        $_ = DIRECTORY_SEPARATOR;
        return app_path('Http'.$_.'Controllers'.$_);
    }

    public static function controllerPath($controller)
    {
        return self::controllersDir().$controller.'.php';
    }

    public static function cbModulesNS($path = '')
    {
        return '\crocodicstudio\crudbooster\Modules\\'.$path;
    }

    public static function ctrlNamespace()
    {
        return 'App\Http\Controllers';
    }
}