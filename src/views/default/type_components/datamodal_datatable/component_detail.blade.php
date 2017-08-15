<?php 
	$table = $form['options']['table'];	
	$optionLabel = $form['options']['column_label'];
	$optionValue = $form['options']['column_value'];
	echo CRUDBooster::first($table,[$optionValue=>$value])->$optionLabel;
?>