<?php

$field_value = $formInput['options']['field_value'];
$field_label = $formInput['options']['field_label'];
switch ($formInput['options']['result_format']) {
    case 'JSON':
        $valueFormat = json_decode($value, true);
        break;
    default:
    case 'COMMA_SEPARATOR':
        $valueFormat = explode(', ', $value);
        break;
    case 'SEMICOLON_SEPARATOR':
        $valueFormat = explode('; ', $value);
        break;
}

$result = [];
foreach ($valueFormat as $d) {
    $q = DB::table($formInput['options']['table'])->where($field_value, $d)->first();
    $result[] = $q->$field_label;
}
echo implode(', ', $result);
?>