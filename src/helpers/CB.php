<?php
namespace crocodicstudio\crudbooster\helpers;

use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Validator;

class CB extends CRUDBooster
{
	//This CB class is for alias of CRUDBooster class


    //alias of echoSelect2Mult
    public function ES2M($values, $table, $id, $name)
    {
        return CRUDBooster::echoSelect2Mult($values, $table, $id, $name);
    }

    public static function listTables()
    {
        if (config('database.default') == 'pgsql') {
            $db_schema = config('crudbooster.MAIN_DB_SCHEMA');
            try {
                $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '" . $db_schema . "'");
            } catch (\Exception $e) {
                $tables = [];
            }
            return $tables;
        } else {
            try {
                $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '" . $db_database . "'");
            } catch (\Exception $e) {
                $tables = [];
            }
            return $tables;
        }
    }
}
