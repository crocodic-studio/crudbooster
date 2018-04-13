<?php
$table = $form['datamodal_table'];
$field = explode(',', $form['datamodal_columns'])[0];
echo CRUDBooster::first($table, ['id' => $value])->$field;
?>