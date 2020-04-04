<?php
$table = $form['datamodal_table'];
$field = explode(',', $form['datamodal_columns'])[0];
echo cb()->first($table, ['id' => $value])->$field;
?>