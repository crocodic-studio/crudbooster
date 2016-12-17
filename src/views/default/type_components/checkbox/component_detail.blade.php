<?php 	

	$data = @unserialize($value);
	if ($data !== false) {
		$value = [];
	    foreach($data as $d) {
	    	$value[] = $d['label'];
	    }
	} else {
	    $value = explode(";",$value);
	}

	foreach($value as $v) {
		echo "<span class='badge'>$v</span> ";
	}	
?>