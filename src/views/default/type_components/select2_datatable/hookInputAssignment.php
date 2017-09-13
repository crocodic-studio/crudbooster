<?php
if (@$ro['options'] && @$ro['options']['multiple'] == true) {
    switch ($ro['options']['multiple_result_format']) {
        case 'JSON':
            $inputdata = json_encode($inputdata);
            break;
        case 'SEMICOLON_SEPARATOR':
            $inputdata = implode('; ', $inputdata);
            break;
        default:
        case 'COMMA_SEPARATOR':
            $inputdata = implode(', ', $inputdata);
            break;
    }
}