<?php
if ($id) {
    $row = DB::table($this->table)->where($this->primary_key, $id)->first();
    if ($row->$name == '') {
        $ai[] = 'required';
    }
}