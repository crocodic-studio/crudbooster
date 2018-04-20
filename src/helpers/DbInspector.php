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
        //$table = CRUDBooster::parseSqlTable($table);

        $primaryKey = self::findPKname($table);

        if (! $primaryKey) {
            return 'id';
        }
        CbCache::put('table_'.$table, 'primaryKey', $primaryKey);

        return $primaryKey;
    }

    /**
     * @param $table
     * @param $colName
     * @return bool
     */
    public static function isNullableColumn($table, $colName)
    {
        $colObj = \DB::getDoctrineSchemaManager()->listTableColumns($table)[$colName];
        if(!$colObj){
           return ;
        }
        return !$colObj->getNotnull();
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
     * @return array
     */
    private static function findPKname($table)
    {
        return \DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey()->getColumns()[0];
    }

    public static function listTables()
    {
        return \DB::getDoctrineSchemaManager()->listTableNames();
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