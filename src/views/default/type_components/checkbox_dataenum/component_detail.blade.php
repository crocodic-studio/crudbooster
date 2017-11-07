<?php
if ($formInput['options']['value']){
    $dataEnum = explode(';', $formInput['options']['enum']);
    $result = [];
    foreach (explode(';', $formInput['options']['value']) as $i => $v) {
        if (in_array($v, explode(';', $value))) {
            $result[] = $dataEnum[$i];
        }
    }
    $result = implode(', ', $result);
} else {
    $result = str_replace(";", ", ", $value);
}
?>
{!! $result !!}