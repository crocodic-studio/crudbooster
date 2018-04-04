<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use crocodicstudio\crudbooster\helpers\Cache as CbCache;
use DB;
use Schema;

class DbInspector
{
    /**
     * @param $table
     * @return bool|null|string
     * @throws \Exception
     */
    public static function findPK($table)
    {
        if (! $table) {
            return 'id';
        }

        if (CbCache::get('table_'.$table, 'primaryKey')) {
            return CbCache::get('table_'.$table, 'primaryKey');
        }
        $table = CRUDBooster::parseSqlTable($table);

        if (! $table['table']) {
            throw new \Exception("parseSqlTable can't determine the table");
        }

        $primaryKey = self::findPKname($table);

        if (! $primaryKey) {
            return 'id';
        }
        CbCache::put('table_'.$table, 'primaryKey', $primaryKey);

        return $primaryKey;
    }

    /**
     * @param $table
     * @param $field
     * @return bool
     */
    public static function isColNull($table, $field)
    {
        $cacheKey = 'field_isNull_'.$table.'_'.$field;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            //MySQL & SQL Server
            $isNULL = DB::select(DB::raw("select IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->IS_NULLABLE;
            $isNULL = ($isNULL == 'YES') ? true : false;
        } catch (\Exception $e) {
            $isNULL = false;
        }
        Cache::forever($cacheKey, $isNULL);

        return $isNULL;
    }

    /**
     * @param $columns
     * @return string
     */
    public static function colName($columns)
    {
        $name_col_candidate = cbConfig('NAME_FIELDS_CANDIDATE');
        $name_col_candidate = explode(',', $name_col_candidate);
        $name_col = '';
        foreach ($columns as $c) {
            foreach ($name_col_candidate as $cc) {
                if (strpos($c, $cc) !== false) {
                    $name_col = $c;
                    break;
                }
            }
            if ($name_col) {
                break;
            }
        }
        if ($name_col == '') {
            $name_col = 'id';
        }

        return $name_col;
    }

    /**
     * @param $table
     * @return array
     */
    public static function getTableCols($table)
    {
        $table = CRUDBooster::parseSqlTable($table);
        $cols = collect(DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table', [
            'database' => $table['database'],
            'table' => $table['table'],
        ]))->map(function ($x) {
            return (array) $x;
        })->toArray();

        return array_column($cols, 'COLUMN_NAME');
    }

    /**
     * @param $table
     * @param $field
     * @return mixed
     */
    public static function getFieldTypes($table, $field)
    {
        $field = 'field_type_'.$table.'_'.$field;

        return Cache::rememberForever($field, function () use ($table, $field) {
            try {
                //MySQL & SQL Server
                $typedata = DB::select(DB::raw("select DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->DATA_TYPE;
            } catch (\Exception $e) {
                $typedata = null;
            }

            return $typedata ?: 'varchar';
        });
    }

    /**
     * @param $fieldName
     * @return bool
     */
    public static function isForeignKey($fieldName)
    {
        $cacheKey = 'isForeignKey_'.$fieldName;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $table = CRUDBooster::getTableForeignKey($fieldName);
        if (! $table) {
            return false;
        }

        $hasTable = Schema::hasTable($table);
        Cache::forever($cacheKey, $hasTable);

        return $hasTable;
    }

    /**
     * @param $table
     * @return null
     */
    private static function getPKforSqlServer($table)
    {
        try {
            $query = "
						SELECT Col.Column_Name,Col.Table_Name from 
						    INFORMATION_SCHEMA.TABLE_CONSTRAINTS Tab, 
						    INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE Col 
						WHERE 
						    Col.Constraint_Name = Tab.Constraint_Name
						    AND Col.Table_Name = Tab.Table_Name
						    AND Constraint_Type = 'PRIMARY KEY'
							AND Col.Table_Name = '$table[table]' 
					";
            $keys = DB::select($query);
            $primaryKey = $keys[0]->Column_Name;
        } catch (\Exception $e) {
            $primaryKey = null;
        }

        return $primaryKey;
    }

    /**
     * @param $table
     * @return array
     */
    private static function findPKname($table)
    {
        if (env('DB_CONNECTION') == 'sqlsrv') {
            return self::getPKforSqlServer($table);
        }
        try {
            $query = "select * from information_schema.COLUMNS where TABLE_SCHEMA = '$table[database]' and TABLE_NAME = '$table[table]' and COLUMN_KEY = 'PRI'";
            $keys = DB::select($query);
            $primaryKey = $keys[0]->COLUMN_NAME;
        } catch (\Exception $e) {
            $primaryKey = null;
        }

        return $primaryKey;
    }

    public static function listTables()
    {
        $multiple_db = cbConfig('MULTIPLE_DATABASE_MODULE') ?: [];
        $db_database = cbConfig('MAIN_DB_DATABASE');

        if ($multiple_db) {
            try {
                $multiple_db[] = cbConfig('MAIN_DB_DATABASE');
                $query_table_schema = implode("','", $multiple_db);
                $tables = DB::select("SELECT CONCAT(TABLE_SCHEMA,'.',TABLE_NAME) FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA != 'mysql' AND TABLE_SCHEMA != 'performance_schema' AND TABLE_SCHEMA != 'information_schema' AND TABLE_SCHEMA != 'phpmyadmin' AND TABLE_SCHEMA IN ('$query_table_schema')");
            } catch (\Exception $e) {
                $tables = [];
            }

            return $tables;
        }

        try {
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '".$db_database."'");
        } catch (\Exception $e) {
            $tables = [];
        }

        return $tables;
    }

    public static function getForeignKey($parent_table, $child_table)
    {
        $parent_table = CRUDBooster::parseSqlTable($parent_table)['table'];
        $child_table = CRUDBooster::parseSqlTable($child_table)['table'];

        if (\Schema::hasColumn($child_table, 'id_'.$parent_table)) {
            return 'id_'.$parent_table;
        }
        return $parent_table.'_id';
    }
}