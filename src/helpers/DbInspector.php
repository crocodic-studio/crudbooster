<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use crocodicstudio\crudbooster\helpers\Cache as CbCache;
use DB;
use Illuminate\Support\Facades\Schema;

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
    public static function isNullableColumn($table, $field)
    {
        return !\DB::getDoctrineSchemaManager()->listTableColumns($table)[$field]->getNotnull();
    }

    /**
     * @param $columns
     * @return string
     */
    public static function colName($columns)
    {
        $nameColCandidate = explode(',', cbConfig('NAME_FIELDS_CANDIDATE'));

        foreach ($columns as $c) {
            foreach ($nameColCandidate as $cc) {
                if (strpos($c, $cc) !== false) {
                    return $c;
                }
            }
        }

        return 'id';
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

        $table = self::getTableForeignKey($fieldName);
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


        $tables = array_map(function ($table) {
            return $table->TABLE_NAME;
        }, $tables);

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

    public static function getTableForeignKey($fieldName)
    {
        if (self::isForeignKey($fieldName)) {
            return str_replace(['_id', 'id_'], '', $fieldName);
        }
    }
}