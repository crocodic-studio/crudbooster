<?php

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Helpers\FontAwesome;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;
use CRUDBooster;

class AdminMenusController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_menus";
        $this->primary_key = "id";
        $this->title_field = "name";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];

        $this->setButtons();

        $id = CRUDBooster::getCurrentId();
        $row = CRUDBooster::first($this->table, $id);
        $row = (Request::segment(3) == 'edit') ? $row : null;

        $id_module = $id_statistic = 0;

        if ($row->type == 'Module') {
            $id_module = DB::table('cms_moduls')->where('path', $row->path)->first()->id;
        } elseif ($row->type == 'Statistic') {
            $row->path = str_replace('statistic-builder/show/', '', $row->path);
            $id_statistic = DB::table('cms_statistics')->where('slug', $row->path)->first()->id;
        }

        $this->script_js = "
			$(function() {
				var current_id = '$id';
				var current_type = '$row->type';
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
					}else if (n == 'Route') {
						$('input[name=path]').attr('placeholder','Please enter the Route');

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

        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Is Active", "name" => "is_active"];

        $this->makeForm($id_module, $id_statistic, $row);
    }

    public function getIndex()
    {
        $this->cbLoader();

        $return_url = Request::fullUrl();

        $page_title = 'Menu Management';

        return view('crudbooster::menus_management', compact('privileges', 'return_url', 'page_title'));
    }

    public function hookBeforeAdd(&$postdata)
    {
        $postdata['parent_id'] = 0;

        $this->setType($postdata);

        unset($postdata['module_slug']);
        unset($postdata['statistic_slug']);

        if ($postdata['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            DB::table('cms_menus')->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }
    }

    public function hookBeforeEdit(&$postdata, $id)
    {
        if ($postdata['is_dashboard'] == 1) {
            //If set dashboard, so unset for first all dashboard
            DB::table('cms_menus')->where('is_dashboard', 1)->update(['is_dashboard' => 0]);
            Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
        }

        $this->setType($postdata);

        unset($postdata['module_slug']);
        unset($postdata['statistic_slug']);
    }

    public function hookAfterDelete($id)
    {
        DB::table('cms_menus')->where('parent_id', $id)->delete();
    }

    public function postSaveMenu()
    {
        $isActive = Request::input('isActive');
        $post = json_decode(Request::input('menus'), true);

        $i = 1;
        foreach ($post[0] as $ro) {
            $pid = $ro['id'];
            if ($ro['children'][0]) {
                $ci = 1;
                foreach ($ro['children'][0] as $c) {
                    $id = $c['id'];
                    DB::table('cms_menus')->where('id', $id)->update(['sorting' => $ci, 'parent_id' => $pid, 'is_active' => $isActive]);
                    $ci++;
                }
            }
            DB::table('cms_menus')->where('id', $pid)->update(['sorting' => $i, 'parent_id' => 0, 'is_active' => $isActive]);
            $i++;
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param $postdata
     */
    private function setType(&$postdata)
    {
        if ($postdata['type'] == 'Statistic') {
            $stat = CRUDBooster::first('cms_statistics', ['id' => $postdata['statistic_slug']])->slug;
            $postdata['path'] = 'statistic-builder/show/'.$stat;
        } elseif ($postdata['type'] == 'Module') {
            $postdata['path'] = CRUDBooster::first('cms_moduls', ['id' => $postdata['module_slug']])->path;
        }
    }

    /**
     * @param $id_module
     * @param $id_statistic
     * @param $row
     */
    private function makeForm($id_module, $id_statistic, $row)
    {
        $this->form = [];
        $this->form[] = [
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
                'ajax_mode' => true,
                'allow_clear' => true,
                'multiple' => true,
                'multiple_result_format' => 'JSON',
            ],
        ];
        $this->form[] = [
            "label" => "Name",
            "name" => "name",
            "type" => "text",
            "required" => true,
            "validation" => "required|min:3|max:255|alpha_spaces",
            "placeholder" => "You can only enter the letter only",
        ];
        $this->form[] = [
            "label" => "Type",
            "name" => "type",
            "type" => "radio",
            "required" => true,
            'dataenum' => ['Module', 'Statistic', 'URL', 'Controller & Method', 'Route'],
            'value' => 'Module',
        ];

        $this->form[] = [
            "label" => "Module",
            "name" => "module_slug",
            "type" => "select2_datatable",
            "options" => [
                "table" => "cms_moduls",
                "field_label" => "name",
                "field_value" => "id",
                "sql_where" => "is_protected = 0",
            ],
            "value" => $id_module,
        ];

        $this->form[] = [
            "label" => "Statistic",
            "name" => "statistic_slug",
            "type" => "select2_datatable",
            "options" => [
                "table" => "cms_statistics",
                "field_label" => "name",
                "field_value" => "id",
            ],
            "style" => "display:none",
            "value" => $id_statistic,
        ];

        $this->form[] = [
            "label" => "Value",
            "name" => "path",
            "type" => "text",
            'help' => 'If you select type controller, you can fill this field with controller name, you may include the method also',
            'placeholder' => 'NameController or NameController@methodName',
            "style" => "display:none",
        ];

        $fontawesome = FontAwesome::cssClass();

        $custom = view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render();
        $this->form[] = [
            'label' => 'Icon',
            'name' => 'icon',
            'type' => 'custom_html',
            'options' => [
                'html' => $custom,
            ],
            'required' => true,
        ];
        $this->form[] = [
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

        $this->form[] = [
            "label" => "Active",
            "name" => "is_active",
            "type" => "radio",
            "required" => true,
            "validation" => "required|integer",
            "dataenum" => ['1|Active', '0|InActive'],
            'value' => '1',
        ];
        $this->form[] = [
            "label" => "Dashboard",
            "name" => "is_dashboard",
            "type" => "radio",
            "required" => true,
            "validation" => "required|integer",
            "dataenum" => ['1|Yes', '0|No'],
            'value' => '0',
        ];
    }

    private function setButtons()
    {
        $this->button_table_action = true;
        $this->button_action_style = "FALSE";
        $this->button_add = false;
        $this->button_delete = true;
        $this->button_edit = true;
        $this->button_detail = true;
        $this->button_show = false;
        $this->button_filter = true;
        $this->button_export = false;
        $this->button_import = false;
    }
}