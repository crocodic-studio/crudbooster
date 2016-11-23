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

