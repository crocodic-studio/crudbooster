<?php
if ($formInput['options']['value']) {
    foreach ($formInput['options']['enum'] as $i => $e) {
        if ($formInput['options']['value'][$i] == $value) {
            echo $e;
            break;
        }
    }
} else {
    echo $value;
}
?>