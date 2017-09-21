<?php
$field_value = $form['options']['field_value'];
$field_label = $form['options']['field_label'];
$rawvalue = $value;
$data = DB::table($form['options']['table']);
if (Schema::hasColumn($form['options']['table'], 'deleted_at')) {
    $data->whereNull('deleted_at');
}
$data = $data->get();
foreach ($data as $d) {
    $option_value = $d->$field_value;
    $option_label = $d->$field_label;
    if ($form['options']['multiple'] == true) {
        switch ($form['options']['multiple_result_format']) {
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