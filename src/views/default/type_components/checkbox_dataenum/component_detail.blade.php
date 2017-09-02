<?php
if ($form['options']['value']) {
    $valueExplode = explode(';', $value);
    $dataValue = explode(';', $form['options']['value']);
    $dataEnum = explode(';', $form['options']['enum']);
    $result = [];
    foreach ($dataValue as $i => $v) {
        if (in_array($v, $valueExplode)) {
            $result[] = $dataEnum[$i];
        }
    }
    echo implode(', ', $result);
} else {
    echo str_replace(";", ", ", $value);
}
?>