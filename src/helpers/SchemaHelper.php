<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2019
 * Time: 12:09 AM
 */

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SchemaHelper
{
    public static function hasColumn($table, $column)
    {
        $columns = cb()->listAllColumns($table);
        return in_array($column,$columns);
    }
}