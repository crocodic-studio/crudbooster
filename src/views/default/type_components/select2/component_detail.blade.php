<?php 
	$datatable = $form['datatable'];
	if($datatable) {
		
		$datatable = explode(',', $datatable);
		$table = $datatable[0];
		$field = $datatable[1];	
		echo CRUDBooster::first($table,['id'=>$value])->$field;
	}
?>