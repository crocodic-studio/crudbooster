<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use Illuminate\Support\Facades\DB;

class ModulesRepo
{
    public static function getControllerName($moduleId)
    {
        return DB::table('cms_moduls')->where('id', $moduleId)->first()->controller;
    }
}