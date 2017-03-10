<?php 
/* 
| ---------------------------------------------------------------------------------------------------------------
| Main Helper of CRUDBooster
| Do not edit or modify this helper unless your modification will be replace if any update from CRUDBooster.
| 
| Homepage : http://crudbooster.com
| ---------------------------------------------------------------------------------------------------------------
|
*/

if(!function_exists('extract_unit')) {	
	/*
	Credits: Bit Repository
	URL: http://www.bitrepository.com/extract-content-between-two-delimiters-with-php.html
	*/
	function extract_unit($string, $start, $end)
	{
	$pos = stripos($string, $start);
	$str = substr($string, $pos);
	$str_two = substr($str, strlen($start));
	$second_pos = stripos($str_two, $end);
	$str_three = substr($str_two, 0, $second_pos);
	$unit = trim($str_three); // remove whitespaces
	return $unit;
	}
}


if(!function_exists('now')) {
	function now() {		
		return date('Y-m-d H:i:s');
	}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/
if(!function_exists('g')) {
function g($name) {
    return Request::get($name);
}
}

if(!function_exists('min_var_export')) {
	function min_var_export($input) {
	    if(is_array($input)) {
	        $buffer = [];
	        foreach($input as $key => $value)
	            $buffer[] = var_export($key, true)."=>".min_var_export($value);
	        return "[".implode(",",$buffer)."]";
	    } else
	        return var_export($input, true);
	}
}
