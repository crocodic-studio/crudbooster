<?php
if ($id) {
    $row = DB::table($this->table)->where($this->primaryKey, $id)->first();
    if ($row->$name == '') {
        $ai[] = 'required';
    }
}