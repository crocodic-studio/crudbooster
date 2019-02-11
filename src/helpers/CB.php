<?php
namespace crocodicstudio\crudbooster\helpers;

use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Validator;

class CB extends CRUDBooster  {
	//This CB class is for alias of CRUDBooster class
	public static function listTables() {
		$tables = array();
		$multiple_db = config('crudbooster.MULTIPLE_DATABASE_MODULE');
		$multiple_db = ($multiple_db)?$multiple_db:array();
		$db_database = config('crudbooster.MAIN_DB_DATABASE');

		if(config('database.default')=='pgsql'){
			$db_schema = config('crudbooster.MAIN_DB_SCHEMA');
			try {
				$tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '".$db_schema."'");
			} catch (\Exception $e) {
				$tables = [];
			}
		}

		return $tables;
	}
}
