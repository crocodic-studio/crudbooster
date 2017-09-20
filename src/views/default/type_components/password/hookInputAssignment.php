<?php
$password_candidate = explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE'));
if (in_array($name, $password_candidate)) {
    if (! empty($this->arr[$name])) {
        $this->arr[$name] = Hash::make($this->arr[$name]);
    } else {
        unset($this->arr[$name]);
    }
}