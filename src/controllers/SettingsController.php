<?php namespace crocodicstudio\crudbooster\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends CBController
{
    public function cbInit()
    {
        $this->module_name = "Settings";
        $this->table = 'cms_settings';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->index_orderby = ['name' => 'asc'];
        $this->button_delete = true;
        $this->button_show = false;
        $this->button_cancel = false;
        $this->button_import = false;
        $this->button_export = false;

        $this->col = [];
        $this->col[] = ["label" => "Nama", "name" => "name", "callback_php" => "ucwords(str_replace('_',' ',%field%))"];
        $this->col[] = ["label" => "Setting", "name" => "content"];

        $this->form = [];

        if (Request::get('group_setting')) {
            $value = Request::get('group_setting');
        } else {
            $value = 'General Setting';
        }

        $this->form[] = ['label' => 'Group', 'name' => 'group_setting', 'value' => $value];
        $this->form[] = ['label' => 'Label', 'name' => 'label'];

        $this->form[] = [
            "label" => "Type",
            "name" => "content_input_type",
            "type" => "select",
            "dataenum" => ["text", "number", "email", "textarea", "wysiwyg", "upload_image", "upload_document", "datepicker", "radio", "select"],
        ];
        $this->form[] = [
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
        $this->form[] = ["label" => "Helper Text", "name" => "helper", "type" => "text"];
    }

    function getShow()
    {
        $this->cbLoader();

        if (! CRUDBooster::isSuperadmin()) {
            CRUDBooster::insertLog(cbLang("log_try_view", ['name' => 'Setting', 'module' => 'Setting']));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $data['page_title'] = urldecode(Request::get('group'));

        return view('crudbooster::setting', $data);
    }

    function hook_before_edit(&$posdata, $id)
    {
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$posdata['group_setting'];
    }

    function getDeleteFileSetting()
    {
        $id = g('id');
        $row = CRUDBooster::first('cms_settings', $id);
        Cache::forget('setting_'.$row->name);
        if (Storage::exists($row->content)) {
            Storage::delete($row->content);
        }
        DB::table('cms_settings')->where('id', $id)->update(['content' => null]);
        CRUDBooster::redirect(Request::server('HTTP_REFERER'), cbLang('alert_delete_data_success'), 'success');
    }

    function postSaveSetting()
    {

        if (! CRUDBooster::isSuperadmin()) {
            CRUDBooster::insertLog(cbLang("log_try_view", ['name' => 'Setting', 'module' => 'Setting']));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $group = Request::get('group_setting');
        $setting = DB::table('cms_settings')->where('group_setting', $group)->get();
        foreach ($setting as $set) {

            $name = $set->name;

            $content = Request::get($set->name);

            if (Request::hasFile($name)) {

                if ($set->content_input_type == 'upload_image') {
                    CRUDBooster::valid([$name => 'image|max:10000'], 'view');
                } else {
                    CRUDBooster::valid([$name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000'], 'view');
                }

                $file = Request::file($name);
                $ext = $file->getClientOriginalExtension();

                //Create Directory Monthly
                $directory = 'uploads/'.date('Y-m');
                Storage::makeDirectory($directory);

                //Move file to storage
                $filename = md5(str_random(5)).'.'.$ext;
                $storeFile = Storage::putFileAs($directory, $file, $filename);
                if ($storeFile) {
                    $content = $directory.'/'.$filename;
                }
            }

            DB::table('cms_settings')->where('name', $set->name)->update(['content' => $content]);

            Cache::forget('setting_'.$set->name);
        }

        return redirect()->back()->with(['message' => 'Your setting has been saved !', 'message_type' => 'success']);
    }

    function hook_before_add(&$arr)
    {
        $arr['name'] = str_slug($arr['label'], '_');
        $this->return_url = CRUDBooster::mainpath("show")."?group=".$arr['group_setting'];
    }

    function hook_after_edit($id)
    {
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        /* REMOVE CACHE */
        Cache::forget('setting_'.$row->name);
    }
}
