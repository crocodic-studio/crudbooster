<?php

namespace crocodicstudio\crudbooster\Modules\StatisticModule;

class StatisticForm
{
    public static function makeForm()
    {
        $form = [];
        $form[] = [
            "label" => "Name",
            "name" => "name",
            "type" => "text",
            "required" => true,
            "validation" => "required|min:3|max:255",
            "placeholder" => "You can only enter the letter only",
        ];

        return $form;
    }
}