<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use DB;

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

        if (CRUDBooster::getCache('table_'.$table, 'primary_key')) {
            return CRUDBooster::getCache('table_'.$table, 'primary_key');
        }
        $table = CRUDBooster::parseSqlTable($table);

        if (! $table['table']) {
            throw new \Exception("parseSqlTable can't determine the table");
        }

        if (env('DB_CONNECTION') == 'sqlsrv') {
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
                $primary_key = $keys[0]->Column_Name;
            } catch (\Exception $e) {
                $primary_key = null;
            }
        } else {
            try {
                $query = "select * from information_schema.COLUMNS where TABLE_SCHEMA = '$table[database]' and TABLE_NAME = '$table[table]' and COLUMN_KEY = 'PRI'";
                $keys = DB::select($query);
                $primary_key = $keys[0]->COLUMN_NAME;
            } catch (\Exception $e) {
                $primary_key = null;
            }
        }

        if (! $primary_key) {
            return 'id';
        }
        CRUDBooster::putCache('table_'.$table, 'primary_key', $primary_key);

        return $primary_key;
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

        $result = $cols;

        $new_result = [];
        foreach ($result as $ro) {
            $new_result[] = $ro['COLUMN_NAME'];
        }

        return $new_result;
    }

    /**
     * @param $table
     * @param $field
     * @return bool
     */
    public static function colExists($table, $field)
    {
        if (! $table) {
            throw new Exception("\$table is empty !", 1);
        }
        if (! $field) {
            throw new Exception("\$field is empty !", 1);
        }

        $table = CRUDBooster::parseSqlTable($table);

        if (CRUDBooster::getCache('table_'.$table, 'column_'.$field)) {
            return CRUDBooster::getCache('table_'.$table, 'column_'.$field);
        }

        $result = DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table AND COLUMN_NAME = :field', [
            'database' => $table['database'],
            'table' => $table['table'],
            'field' => $field,
        ]);

        if (count($result) > 0) {
            CRUDBooster::putCache('table_'.$table, 'column_'.$field, 1);

            return true;
        }

        CRUDBooster::putCache('table_'.$table, 'column_'.$field, 0);

        return false;
    }
}