<?php

namespace Crocodicstudio\Crudbooster\Modules\MenuModule;

use Crocodicstudio\Crudbooster\Controllers\Helpers\FontAwesome;

class MenusForm
{
    /**
     * @param $moduleId
     * @param $statisticId
     * @param $row
     * @return array
     */
    static function makeForm($statisticId, $moduleId, $row)
    {
        $form = [];
        $form[] = [
            'label' => 'Privilege(s)',
            'name' => 'cms_roles',
            'type' => 'select2_datatable',
            'placeholder' => '** You can choose multiple privileges',  // todo: translation
            'options' => [
                'table' => 'cms_roles',
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
            'label' => 'Name',
            'name' => 'name',
            'type' => 'text',
            'required' => true,
            'validation' => 'required|min:3|max:255|alpha_spaces',
            'placeholder' => 'You can only enter the letter only', //todo: translation needed
        ];
        $form[] = [
            'label' => 'Type',
            'name' => 'type',
            'type' => 'radio',
            'required' => true,
            'dataenum' => MenuTypes::all(),
            'value' => MenuTypes::Module,
        ];

        $form[] = [
            'label' => 'Module',
            'name' => 'module_slug',
            'type' => 'select2_datatable',
            'options' => [
                'table' => 'cms_modules',
                'field_label' => 'name',
                'field_value' => 'id',
                'sql_where' => 'is_protected = 0',
            ],
            'value' => $moduleId,
        ];

        $form[] = [
            'label' => 'Statistic',
            'name' => 'statistic_slug',
            'type' => 'select2_datatable',
            'options' => [
                'table' => 'cms_statistics',
                'field_label' => 'name',
                'field_value' => 'id',
            ],
            'style' => 'display:none',
            'value' => $statisticId,
        ];

        $form[] = [
            'label' => 'Value',
            'name' => 'path',
            'type' => 'text',
            'help' => 'If you select type controller, you can fill this field with controller name, you may include the method also', // todo: translation
            'placeholder' => 'NameController or NameController@methodName', // todo: translation
            'style' => 'display:none',
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
            'label' => 'Active',
            'name' => 'is_active',
            'type' => 'radio',
            'required' => true,
            'validation' => 'required|integer',
            'dataenum' => ['1|Active', '0|InActive'],
            'value' => '1',
        ];

        $form[] = [
            'label' => 'Dashboard',
            'name' => 'is_dashboard',
            'type' => 'radio',
            'required' => true,
            'validation' => 'required|integer',
            'dataenum' => ['1|Yes', '0|No'],  // todo: translation
            'value' => '0',
        ];

        return $form;
    }
}