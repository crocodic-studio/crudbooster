<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use Illuminate\Support\Facades\DB;

class ModulesRepo
{
    public static function getControllerName($moduleId)
    {
        return self::find($moduleId)->controller;
    }

    public static function find($id)
    {
        return DB::table('cms_moduls')->where('id', $id)->first();
    }
}