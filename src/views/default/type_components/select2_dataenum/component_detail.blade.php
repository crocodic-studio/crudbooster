<?php
if ($form['options']['value']) {
    foreach ($form['options']['enum'] as $i => $e) {
        if ($form['options']['value'][$i] == $value) {
            echo $e;
            break;
        }
    }
} else {
    echo $value;
}
?>