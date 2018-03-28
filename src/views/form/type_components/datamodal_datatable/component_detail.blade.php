<?php
$table = $formInput['options']['table'];
$optionLabel = $formInput['options']['column_label'];
$optionValue = $formInput['options']['column_value'];
echo CRUDBooster::first($table, [$optionValue => $value])->$optionLabel;
?>