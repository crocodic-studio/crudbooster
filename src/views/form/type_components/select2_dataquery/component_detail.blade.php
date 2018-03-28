<?php
$field_value = $formInput['options']['field_value'];
$field_label = $formInput['options']['field_label'];
$rawvalue = $value;
$data = DB::select(DB::raw($formInput['options']['query']));
foreach ($data as $d) {
    $option_value = $d->$field_value;
    $option_label = $d->$field_label;
    if ($formInput['options']['multiple'] == true) {
        switch ($formInput['options']['multiple_result_format']) {
            case 'JSON':
                $value = json_decode($rawvalue, true) ?: [];
                if (in_array($option_value, $value)) {
                    echo $option_label.', ';
                }
                break;
            default:
            case 'COMMA_SEPARATOR':
                $value = explode(', ', $rawvalue);
                if (in_array($option_value, $value)) {
                    echo $option_label.', ';
                }
                break;
            case 'SEMICOLON_SEPARATOR':
                $value = explode('; ', $rawvalue);
                if (in_array($option_value, $value)) {
                    echo $option_label.', ';
                }
                break;
        }
    } else {
        if ($option_value == $value) {
            echo $option_label;
            break;
        }
    }
}
?>