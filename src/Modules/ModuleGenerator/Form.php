<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

use Crocodicstudio\Crudbooster\controllers\Helpers\FontAwesome;
use Crocodicstudio\Crudbooster\helpers\CRUDBooster;

class Form
{
    public static function makeForm()
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

        $form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'custom_html', 'options' => ['html' => self::MakeIconList()], 'required' => true];

        $form[] = ['label' => 'Path', 'name' => 'path', 'required' => true, 'placeholder' => 'Optional'];
        $form[] = ['label' => 'Controller', 'name' => 'controller', 'type' => 'text', 'placeholder' => '(Optional) Auto Generated'];

        if (in_array(CRUDBooster::getCurrentMethod(), ['getAdd', 'postAddSave'])) {
            return $form;
        }

        return self::addStep4fields($form);
    }

    /**
     * @param $form
     * @return array
     */
    private static function addStep4fields($form)
    {

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
     * @return string
     * @throws \Throwable
     */
    private static function MakeIconList()
    {
        $fontawesome = FontAwesome::cssClass();

        return view('crudbooster::components.list_icon', compact('fontawesome'))->render();
    }
}