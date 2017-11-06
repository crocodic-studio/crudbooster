<?php
if ($formInput['options']['query']) {
    $query = $formInput['options']['query'];
    $fieldLabel = $formInput['options']['field_label'];
    $fieldValue = $formInput['options']['field_value'];
    echo CRUDBooster::first($query, [$fieldValue => $value])->$fieldLabel;
}
?>