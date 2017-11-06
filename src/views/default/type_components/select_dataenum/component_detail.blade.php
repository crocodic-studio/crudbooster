<?php
if ($formInput['options']['table']) {
    $table = $formInput['options']['table'];
    $fieldLabel = $formInput['options']['field_label'];
    $fieldValue = $formInput['options']['field_value'];
    echo CRUDBooster::first($table, [$field_value => $value])->$fieldLabel;
}
?>