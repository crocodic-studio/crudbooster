<?php 

\Validator::extend('alpha_spaces', function ($attribute, $value) {
    // This will only accept alpha and spaces. 
    // If you want to accept hyphens use: /^[\pL\s-]+$/u.
    return preg_match('/^[\pL\s]+$/u', $value); 
},'The :attribute should be letters only');