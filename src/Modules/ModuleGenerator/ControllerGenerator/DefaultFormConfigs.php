<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

class DefaultFormConfigs
{
    /**
     * @param $table
     * @return array
     */
    public static function defaultConfigForFields($table)
    {
        return [
            'isPassword' => [
                'type' => 'password',
                'validation' => 'min:5|max:32|required',
                'help' => cbTrans("text_default_help_password"),
            ],
            'isImage' => [
                'type' => 'upload',
                'validation' => 'required|image',
                'help' => cbTrans('text_default_help_upload'),
            ],
            'isGeographical' => [
                'type' => 'hidden',
                'validation' => 'required|numeric',
            ],
            'isPhone' => [
                'type' => 'number',
                'validation' => 'required|numeric',
                'placeholder' => cbTrans('text_default_help_number'),
            ],
            'isEmail' => [
                'type' => 'email',
                'validation' => 'require|email|unique:'.$table,
                'placeholder' => cbTrans('text_default_help_email'),
            ],
            'isNameField' => [
                'type' => 'text',
                'validation' => 'required|string|min:3|max:70',
                'placeholder' => cbTrans('text_default_help_text'),
            ],
            'isUrlField' => [
                'type' => 'text',
                'validation' => 'required|url',
                'placeholder' => cbTrans('text_default_help_url'),
            ],
        ];
    }
}