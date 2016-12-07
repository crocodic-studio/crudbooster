<?php 
	$value = explode(";",$value);
	foreach($value as $v) {
		echo "<span class='badge'>$v</span> ";
	}	
?>