<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:00 PM
 */

namespace crocodicstudio\crudbooster\types\checkbox;

class CheckboxHelper
{
    public static function parseValuesToArray($values) {
        $data = explode(";",$values);
        return $data;
    }
    
}