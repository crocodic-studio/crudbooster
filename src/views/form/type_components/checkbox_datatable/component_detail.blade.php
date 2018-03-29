<?php

$field_value = $formInput['options']['field_value'];
$field_label = $formInput['options']['field_label'];
$format = $formInput['options']['result_format'];

if ($format == 'JSON') {
    $valueFormat = json_decode($value, true);
} elseif ($format == 'SEMICOLON_SEPARATOR') {
    $valueFormat = explode('; ', $value);
} else {
    $valueFormat = explode(', ', $value);
}

$result = [];
foreach ($valueFormat as $d) {
    $q = DB::table($formInput['options']['table'])->where($field_value, $d)->first();
    $result[] = $q->$field_label;
}
echo implode(', ', $result);
?>