<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

class MenuJavascript
{
    /**
     * @param $id
     * @param $type
     * @return string
     */
    static function setJs($id, $type)
    {
        return "
			$(function() {
				var current_id = '$id';
				var current_type = '$type';
				var type_menu = $('input[name=type]').val();
				type_menu = (current_type)?current_type:type_menu;
				if(type_menu == 'Module') {
					$('#form-group-module_slug').show();
					$('#form-group-statistic_slug,#form-group-path').hide();
					$('#module_slug').prop('required',true);
					$('#form-group-module_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
				}else if(type_menu == 'Statistic') {
					$('#form-group-statistic_slug').show();
					$('#module_slug').prop('required',false);
					$('#form-group-module_slug,#form-group-path').hide();
					$('#statistic_slug').prop('required',true);
					$('#form-group-statistic_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
				}else{
					$('#module_slug').prop('required',false);
					$('#form-group-module_slug,#form-group-statistic_slug').hide();
					$('#form-group-path').show();
				}


				function format(icon) {          
	                  var originalOption = icon.element;
	                  var label = $(originalOption).text();
	                  var val = $(originalOption).val();
	                  if(!val) return label;
	                  var \$resp = $('<span><i style=\"margin-top:5px\" class=\"pull-right ' + $(originalOption).val() + '\"></i> ' + $(originalOption).data('label') + '</span>');
	                  return \$resp;
	              }
	              $('#list-icon').select2({
	                  width: \"100%\",
	                  templateResult: format,
	                  templateSelection: format
	              });

				$('input[name=type]').click(function() {
					var default_placeholder_path = 'NameController@methodName';
					var n = $(this).val();
					var isCheck = $(this).prop('checked');
					console.log('Click the module type '+n);
					$('#module_slug').prop('required',false);
					$('input[name=path]').attr('placeholder',default_placeholder_path);
					if(n == 'Module') {
						$('#form-group-path').hide();
						$('#form-group-statistic_slug').hide();
						$('#statistic_slug,#path').prop('required',false);

						$('#form-group-module_slug').show();
						$('#module_slug').prop('required',true);
						$('#form-group-module_slug label .text-danger').remove();
						$('#form-group-module_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
					}else if (n == 'Statistic') {
						$('#form-group-path').hide();
						$('#form-group-module_slug').hide();
						$('#module_slug,#path').prop('required',false);

						$('#form-group-statistic_slug').show();
						$('#statistic_slug').prop('required',true);
						$('#form-group-statistic_slug label .text-danger').remove();
						$('#form-group-statistic_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
					}else if (n == 'URL') {
						$('input[name=path]').attr('placeholder','Please enter your URL');

						$('#path').prop('required',true);
						$('#form-group-path label .text-danger').remove();
						$('#form-group-path label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');

						$('#form-group-path').show();
						$('#form-group-module_slug,#form-group-statistic_slug').hide();
                        $('#statistic_slug,#path').prop('required',false);
					}else if (n == 'Route') {
						$('input[name=path]').attr('placeholder','Please enter the Route');
                        $('#statistic_slug,#path').prop('required',false);
						$('#path').prop('required',true);
						$('#form-group-path label .text-danger').remove();
						$('#form-group-path label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');

						$('#form-group-path').show();
						$('#form-group-module_slug,#form-group-statistic_slug').hide();					
					}else {
						$('#module_slug,#statistic_slug').prop('required',false);
						
						$('#path').prop('required',true);
						$('#form-group-path label .text-danger').remove();
						$('#form-group-path label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');

						$('#form-group-path').show();
						$('#form-group-module_slug,#form-group-statistic_slug').hide();
					}
				})
			})
			";
    }

}