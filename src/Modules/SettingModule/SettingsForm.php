<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

class SettingsForm
{
    public static function makeForm($value)
    {
        $form = [];

        $form[] = ['label' => 'Group', 'name' => 'group_setting', 'value' => $value];
        $form[] = ['label' => 'Label', 'name' => 'label'];

        $form[] = [
            "label" => "Type",
            "name" => "content_input_type",
            "type" => "select_dataenum",
            'options' => ["enum" => ["text", "number", "email", "textarea", "wysiwyg", "upload_image", "upload_document", "datepicker", "radio", "select"]],
        ];
        $form[] = [
            "label" => "Radio / Select Data",
            "name" => "dataenum",
            "placeholder" => "Example : abc,def,ghi",
            "jquery" => "
			function show_radio_data() {
				var cit = $('#content_input_type').val();
				if(cit == 'radio' || cit == 'select') {
					$('#form-group-dataenum').show();	
				}else{
					$('#form-group-dataenum').hide();
				}					
			}
			$('#content_input_type').change(show_radio_data);
			show_radio_data();
			",
        ];
        $form[] = ["label" => "Helper Text", "name" => "helper", "type" => "text"];

        return $form;
    }
}