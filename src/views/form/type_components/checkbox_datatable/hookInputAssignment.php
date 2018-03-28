<?php
if (is_array($inputdata)) {
    $table_checkbox = $formInput['options']['table'];
    $field_checkbox = $formInput['options']['field_label'];
    $data_checkbox = DB::table($table_checkbox)->whereIn($formInput['options']['field_value'], $inputdata)->pluck($field_checkbox)->toArray();
    $this->arr[$name] = implode(";", $data_checkbox);
}