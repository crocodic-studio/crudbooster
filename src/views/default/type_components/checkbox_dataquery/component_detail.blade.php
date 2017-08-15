<?php 	
	$data = DB::select(DB::raw($form['options']['query']));
	$field_value = $form['options']['field_value'];
	$field_label = $form['options']['field_label'];
	switch ($form['options']['result_format']) {
		case 'JSON':
			$valueFormat = json_decode($value,true);
			$result = [];
			foreach($data as $d) {
				if(in_array($d->$field_value, $valueFormat)) {
					$result[] = $d->$field_label;
				}
			}
			echo implode(', ',$result);
			break;		
		default:
		case 'COMMA_SEPARATOR':
			$valueFormat = explode(', ',$value);
			$result = [];
			foreach($data as $d) {
				if(in_array($d->$field_value, $valueFormat)) {
					$result[] = $d->$field_label;
				}
			}
			echo implode(', ',$result);		
			break;
		case 'SEMICOLON_SEPARATOR':
			$valueFormat = explode('; ',$value);
			$result = [];
			foreach($data as $d) {
				if(in_array($d->$field_value, $valueFormat)) {
					$result[] = $d->$field_label;
				}
			}
			echo implode(', ',$result);
			break;
	}
?>