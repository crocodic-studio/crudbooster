<?php 	
	if($form['options']['table']) {		
		$table = $form['options']['table'];
		$fieldLabel = $form['options']['field_label'];
		$fieldValue = $form['options']['field_value'];
		echo CRUDBooster::first($table,[$fieldValue=>$value])->$fieldLabel;
	}
?>