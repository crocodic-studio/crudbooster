<?php
if ($id) {
    $row = DB::table($this->ctrl->table)->where($this->ctrl->primaryKey, $id)->first();
    if ($row->$name == '') {
        $ai[] = 'required';
    }
}