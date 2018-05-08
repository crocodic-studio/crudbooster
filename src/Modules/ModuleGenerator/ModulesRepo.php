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

    public static function getByTableName($table)
    {
        return DB::table('cms_moduls')->where('table_name', $table)->first();
    }

    /**
     * @param $path
     * @return mixed
     */
    public static function modulePathExists($path)
    {
        return (boolean) DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count();
    }

    public static function updateById($id, $data)
    {
        return DB::table('cms_moduls')->where('id', $id)->update($data);
    }

    public static function getByPath($modulepath)
    {
        return DB::table('cms_moduls')->where('path', $modulepath)->first();
    }

    public static function countByPath($modulepath)
    {
        return DB::table('cms_moduls')->where('path', $modulepath)->count();
    }
}