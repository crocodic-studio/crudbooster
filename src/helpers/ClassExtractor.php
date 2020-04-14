<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/14/2020
 * Time: 11:24 PM
 */

namespace crocodicstudio\crudbooster\helpers;


trait ClassExtractor
{
    public function toArray() {
        $result = [];
        foreach ($this as $key=>$val) {
            $result[$key] = $val;
        }
        return $result;
    }

}