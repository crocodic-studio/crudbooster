<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use DB;
use Illuminate\Support\Facades\Schema;

class DbInspector
{
    /**
     * @param $table
     * @return string
     * @throws \Exception
     */
    public static function findPK(string $table): string
    {
        if (! $table) {
            return 'id';
        }

        return cache()->remember('CrudBooster_pk_'.$table, 10, function () use ($table) {
            return self::findPKname($table) ?: 'id';
        });
    }

    /**
     * @param $table
     * @return array
     */
    private static function findPKname($table)
    {
        $cols = \DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey()->getColumns();
        if (! empty($cols)) {
            return $cols[0];
        }
    }

    /**
     * @param $table
     * @param $colName
     * @return bool
     */
    public static function isNullableColumn($table, $colName)
    {
        $colObj = \DB::getDoctrineSchemaManager()->listTableColumns($table)[$colName];
        if (! $colObj) {
            return;
        }

        return ! $colObj->getNotnull();
    }

    /**
     * @param $columns
     * @return string
     */
    public static function colName(array $columns): string
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

    public static function getTableForeignKey($fieldName)
    {
        if (self::isForeignKey($fieldName)) {
            return str_replace(['_id', 'id_'], '', $fieldName);
        }
    }

    public static function listTables(): array
    {
        return \DB::getDoctrineSchemaManager()->listTableNames();
    }

    public static function getForeignKey($parentTable, $childTable): string
    {
        $parentTable = CRUDBooster::parseSqlTable($parentTable)['table'];
        $childTable = CRUDBooster::parseSqlTable($childTable)['table'];

        if (\Schema::hasColumn($childTable, 'id_'.$parentTable)) {
            return 'id_'.$parentTable;
        }

        return $parentTable.'_id';
    }
}