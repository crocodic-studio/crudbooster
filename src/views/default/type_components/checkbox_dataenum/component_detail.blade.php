<?php
if ($form['options']['value']){
    $dataEnum = explode(';', $form['options']['enum']);
    $result = [];
    foreach (explode(';', $form['options']['value']) as $i => $v) {
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