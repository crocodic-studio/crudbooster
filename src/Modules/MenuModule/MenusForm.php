<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use crocodicstudio\crudbooster\controllers\Helpers\FontAwesome;

class MenusForm
{
    /**
     * @param $module_id
     * @param $statistic_id
     * @param $row
     * @return array
     */
    static function makeForm($statistic_id, $module_id, $row)
    {
        $form = [];
        $form[] = [
            "label" => "Privilege(s)",
            "name" => "cms_privileges",
            "type" => "select2_datatable",
            "placeholder" => "** You can choose multiple privileges",
            "options" => [
                'table' => 'cms_privileges',
                'field_label' => 'name',
                'field_value' => 'name',
                'sql_where' => null,
                'sql_orderby' => null,
                'limit' => null,
                'ajax_mode' => false,
                'allow_clear' => true,
                'multiple' => false,
                'multiple_result_format' => 'JSON',
            ],
        ];
        $form[] = [
            "label" => "Name",
            "name" => "name",
            "type" => "text",
            "required" => true,
            "validation" => "required|min:3|max:255|alpha_spaces",
            "placeholder" => "You can only enter the letter only",
        ];
        $form[] = [
            "label" => "Type",
            "name" => "type",
            "type" => "radio",
            "required" => true,
            'dataenum' => ['Module', 'Statistic', 'URL', 'Controller & Method', 'Route'],
            'value' => 'Module',
        ];

        $form[] = [
            "label" => "Module",
            "name" => "module_slug",
            "type" => "select2_datatable",
            "options" => [
                "table" => "cms_moduls",
                "field_label" => "name",
                "field_value" => "id",
                "sql_where" => "is_protected = 0",
            ],
            "value" => $module_id,
        ];

        $form[] = [
            "label" => "Statistic",
            "name" => "statistic_slug",
            "type" => "select2_datatable",
            "options" => [
                "table" => "cms_statistics",
                "field_label" => "name",
                "field_value" => "id",
            ],
            "style" => "display:none",
            "value" => $statistic_id,
        ];

        $form[] = [
            "label" => "Value",
            "name" => "path",
            "type" => "text",
            'help' => 'If you select type controller, you can fill this field with controller name, you may include the method also',
            'placeholder' => 'NameController or NameController@methodName',
            "style" => "display:none",
        ];

        $fontawesome = FontAwesome::cssClass();

        $form[] = [
            'label' => 'Icon',
            'name' => 'icon',
            'type' => 'custom_html',
            'options' => [
                'html' => view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render(),
            ],
            'required' => true,
        ];
        $form[] = [
            'label' => 'Color',
            'name' => 'color',
            'type' => 'select2_dataenum',
            'options' => [
                'enum' => ['normal', 'red', 'green', 'aqua', 'light-blue', 'yellow', 'muted'],
                'value' => [],
                'allow_clear' => false,
                'multiple' => false,
                'multiple_result_format' => null,
            ],
            'required' => true,
            'value' => 'normal',
        ];

        $form[] = [
            "label" => "Active",
            "name" => "is_active",
            "type" => "radio",
            "required" => true,
            "validation" => "required|integer",
            "dataenum" => ['1|Active', '0|InActive'],
            'value' => '1',
        ];

        $form[] = [
            "label" => "Dashboard",
            "name" => "is_dashboard",
            "type" => "radio",
            "required" => true,
            "validation" => "required|integer",
            "dataenum" => ['1|Yes', '0|No'],
            'value' => '0',
        ];

        return $form;
    }
}