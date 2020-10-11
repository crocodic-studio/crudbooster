<?php 
namespace crocodicstudio\crudbooster\helpers;

class CB extends CRUDBooster  {
	//This CB class is for alias of CRUDBooster class
	
	
    //alias of echoSelect2Mult
    public function ES2M($values, $table, $id, $name) {
        return CRUDBooster::echoSelect2Mult($values, $table, $id, $name);
    }
}
