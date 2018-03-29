<?php
$data = DB::select(DB::raw($formInput['options']['query']));
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
foreach ($data as $d) {
    if (in_array($d->$field_value, $valueFormat)) {
        $result[] = $d->$field_label;
    }
}
echo implode(', ', $result);
?>