<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2019
 * Time: 12:23 AM
 */

namespace crocodicstudio\crudbooster\helpers;


class MiscellanousSingleton
{
    private $data;

    /**
     * @return mixed
     */
    public function getData($key)
    {
        return @$this->data[$key];
    }

    public function hasData($key) {
        $data = $this->data;
        if(isset($data[$key])) return true;
        else return false;
    }

    public function setData($key, $value): void
    {
        $this->data[$key] = $value;
    }



}