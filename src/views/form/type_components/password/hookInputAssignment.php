<?php

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;

if (FieldDetector::isPassword($name)) {
    if (! empty($this->arr[$name])) {
        $this->arr[$name] = Hash::make($this->arr[$name]);
    } else {
        unset($this->arr[$name]);
    }
}