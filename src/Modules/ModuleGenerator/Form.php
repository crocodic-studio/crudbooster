<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use crocodicstudio\crudbooster\controllers\Helpers\FontAwesome;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class Form
{
    public static function makeForm($table)
    {
        $form = [];
        $form[] = ['label' => 'Name', 'name' => 'name', 'placeholder' => 'Module name here', 'required' => true];

        $form[] = [
            'label' => "Table Name",
            'name' => "table_name",
            'type' => "select2_dataenum",
            "options" => ['enum' => CRUDBooster::listCbTables()],
            'required' => true,
        ];

        $form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'custom_html', 'options' => ['html' => self::MakeIconList($table)], 'required' => true];

        $form[] = ['label' => 'Path', 'name' => 'path', 'required' => true, 'placeholder' => 'Optional'];
        $form[] = ['label' => 'Controller', 'name' => 'controller', 'type' => 'text', 'placeholder' => '(Optional) Auto Generated'];

        if (in_array(CRUDBooster::getCurrentMethod(), ['getAdd', 'postAddSave'])) {
            return ;
        }

        $form[] = [
            'label' => "Global Privilege",
            'name' => "global_privilege",
            'type' => "radio",
            'dataenum' => ['0|No', '1|Yes'],
            'value' => 0,
            'help' => 'Global Privilege allows you to make the module to be accessible by all privileges',
            'exception' => true,
        ];

        $form[] = [
            'label' => 'Button Action Style',
            'name' => 'button_action_style',
            'type' => 'radio',
            'dataenum' => ['button_icon', 'button_icon_text', 'button_text', 'dropdown'],
            'value' => 'button_icon',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Table Action",
            'name' => "button_table_action",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Add",
            'name' => "button_add",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Delete",
            'name' => "button_delete",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Edit",
            'name' => "button_edit",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Detail",
            'name' => "button_detail",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Show",
            'name' => "button_show",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => "Button Filter",
            'name' => "button_filter",
            'type' => "radio",
            'dataenum' => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $form[] = [
            'label' => 'Button Export',
            'name' => 'button_export',
            'type' => 'radio',
            'dataenum' => ['Yes', 'No'],
            'value' => 'No',
            'exception' => true,
        ];
        $form[] = [
            'label' => 'Button Import',
            'name' => 'button_import',
            'type' => 'radio',
            'dataenum' => ['Yes', 'No'],
            'value' => 'No',
            'exception' => true,
        ];

        return $form;
    }

    /**
     * @param $table
     * @return string
     * @throws \Throwable
     */
    private static function MakeIconList($table)
    {
        $fontawesome = FontAwesome::cssClass();
        $row = CRUDBooster::first($table, CRUDBooster::getCurrentId());

        return view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render();
    }
}