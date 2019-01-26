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
	
	
    //alias of echoSelect2Mult
    public function ES2M($values, $table, $id, $name) {
        return CRUDBooster::echoSelect2Mult($values, $table, $id, $name);
    }
}
