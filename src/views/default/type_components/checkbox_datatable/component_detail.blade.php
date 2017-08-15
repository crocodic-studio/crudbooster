<?php 	
	
	$field_value = $form['options']['field_value'];
	$field_label = $form['options']['field_label'];
	switch ($form['options']['result_format']) {
		case 'JSON':
			$valueFormat = json_decode($value,true);
			break;		
		default:
		case 'COMMA_SEPARATOR':
			$valueFormat = explode(', ',$value);		
			break;
		case 'SEMICOLON_SEPARATOR':
			$valueFormat = explode('; ',$value);
			break;
	}

	$result = [];
	foreach($valueFormat as $d) {
		$q = DB::table($form['options']['table'])->where($field_value,$d)->first();
		$result[] = $q->$field_label;
	}
	echo implode(', ',$result);
?>