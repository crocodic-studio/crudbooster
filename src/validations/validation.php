<?php 

\Validator::extend('alpha_spaces', function ($attribute, $value) {
    // This will only accept alpha and spaces. 
    // If you want to accept hyphens use: /^[\pL\s-]+$/u.
    return preg_match('/^[\pL\s]+$/u', $value); 
},'The :attribute should be letters and spaces only');

\Validator::extend('alpha_num_spaces', function ($attribute, $value) {
    // This will only accept alphanumeric and spaces. 
    return preg_match('/^[a-zA-Z0-9\s]+$/', $value); 
},'The :attribute should be alphanumeric characters and spaces only');