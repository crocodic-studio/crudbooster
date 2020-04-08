<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/8/2020
 * Time: 7:45 PM
 */

namespace crocodicstudio\crudbooster\inputs;


abstract class InputContainer
{
    public static function add($label, $column) {
        return new static($label, $column);
    }
}