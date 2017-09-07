<?php
if ($form['options']['query']) {
    $query = $form['options']['query'];
    $fieldLabel = $form['options']['field_label'];
    $fieldValue = $form['options']['field_value'];
    echo CRUDBooster::first($query, [$fieldValue => $value])->$fieldLabel;
}
?>