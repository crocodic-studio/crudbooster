<?php namespace crocodicstudio\crudbooster\singleton;

class CRUDBooster
{
    private $data;

    public function set($key, $value) {
        if($this->data[$key]) {
            if(is_array($value)) {
                $this->data[$key] = array_merge($this->data[$key], $value);
            } else {
                $this->data[$key] = $value;
            }
        } else {
            $this->data[$key] = $value;
        }
    }

    public function get($key) {
        return $this->data[$key];
    }

}