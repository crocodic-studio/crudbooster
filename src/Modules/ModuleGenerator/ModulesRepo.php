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
        return self::first(['id' => $id]);
    }

    private static function where($conditions)
    {
        return DB::table('cms_moduls')->where($conditions);
    }

    public static function getByTableName($table)
    {
        return self::first(['table_name' => $table]);
    }

    /**
     * @param $path
     * @return mixed
     */
    public static function modulePathExists($path)
    {
        return (boolean) self::count(['path' => $path, 'deleted_at' => null]);
    }

    public static function updateById($id, $data)
    {
        return self::where(['id' => $id])->update($data);
    }

    public static function getByPath($modulepath)
    {
        return self::first(['path' => $modulepath]);
    }

    public static function countByPath($modulepath)
    {
        return self::count(['path' => $modulepath]);
    }

    /**
     * @param $conditions
     * @return mixed
     */
    private static function first($conditions)
    {
        return self::where($conditions)->first();
    }

    /**
     * @param $conditions
     * @return mixed
     */
    private static function count($conditions)
    {
        return self::where($conditions)->count();
    }
}