<?php
if (is_array($inputdata)) {
    $table_checkbox = $form['options']['table'];
    $field_checkbox = $form['options']['field_label'];
    $data_checkbox = DB::table($table_checkbox)->whereIn($form['options']['field_value'], $inputdata)->pluck($field_checkbox)->toArray();
    $this->arr[$name] = implode(";", $data_checkbox);
}