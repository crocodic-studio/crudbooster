@php 
	$format = $form['options']['detail_format'];
	$format = ($format)?:'Y-m-d';
	echo date($format, strtotime($value));
@endphp