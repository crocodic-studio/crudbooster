<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/8/2020
 * Time: 9:10 PM
 */

namespace crocodicstudio\crudbooster\inputs;


trait InputTrait
{
    public function __construct($label, $column)
    {
        $this->label = $label;
        $this->name = $column;
    }
    
    public function toArray() {
        $result = [];
        foreach($this as $key => $val) {
            $result[$key] = $val;
        }
        return $result;
    }
}