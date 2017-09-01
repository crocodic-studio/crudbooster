<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
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

class AdminSettingsController extends CBController {

	public function cbInit() {		
		$this->table         = 'cms_settings';
		$this->title_field   = "name";		
		$this->index_orderby = array('name'=>'asc');
		$this->button_delete = true;
		$this->button_show   = false;
		$this->button_cancel = false;
		$this->button_import = false;
		$this->button_export = false;

		$this->col = array();		

		$this->form = array();
		
		
		if(Request::get('group_setting')) {
			$value = Request::get('group_setting');
		}else{
			$value = 'General Setting';
		}

		$this->form[] = array('label'=>'Group','name'=>'group_setting','value'=>$value);
		$this->form[] = array('label'=>'Label','name'=>'label');

		$this->form[] = array("label"=>"Type","name"=>"content_input_type","type"=>"select_dataenum",'options'=>["enum"=>array("text","number","email","textarea","wysiwyg","upload_image","upload_document","datepicker","radio","select")]);		
		$this->form[] = array("label"=>"Radio / Select Data","name"=>"dataenum","placeholder"=>"Example : abc,def,ghi","jquery"=>"
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
			");
		$this->form[] = array("label"=>"Helper Text","name"=>"helper","type"=>"text");				
		
		
	}

	function getShow() {
		$this->cbLoader();

        $this->allowOnlySuperAdmin();

		$data['page_title'] = urldecode(Request::get('group'));		
		return view('crudbooster::setting',$data);
	} 
	
	function hook_before_edit(&$posdata,$id) {
		$this->return_url = CRUDBooster::mainpath("show")."?group=".$posdata['group_setting'];
	}

	function getDeleteFileSetting() {
		$id = g('id');
		$row = CRUDBooster::first('cms_settings',$id);
		if(Storage::exists($row->content)) Storage::delete($row->content);
		DB::table('cms_settings')->where('id',$id)->update(['content'=>NULL]);
		CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans('alert_delete_data_success'),'success');		
	}	


	function postSaveSetting() {

        $this->allowOnlySuperAdmin();
		
		$group = Request::get('group_setting');
		$setting = DB::table('cms_settings')->where('group_setting',$group)->get();
		foreach($setting as $set) {
			
			$name = $set->name;

			$content = Request::get($set->name);

			if (Request::hasFile($name))
			{
                $content = $this->uploadFile($set);
			}


			DB::table('cms_settings')->where('name',$set->name)->update(['content'=>$content]);

			Cache::forget('setting_'.$set->name);
		}
        return CRUDBooster::backWithMsg('Your setting has been saved !');
	}

	function hook_before_add(&$arr) {
		$arr['name'] = str_slug($arr['label'],'_');
		$this->return_url = CRUDBooster::mainpath("show")."?group=".$arr['group_setting'];
	}

	function hook_after_edit($id) {
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		/* REMOVE CACHE */
		Cache::forget('setting_'.$row->name);
	}

    /**
     * @param $name
     * @param $set
     */
    private function validateFileType($set)
    {
        $name = $set->name;
        $rules = [$name => 'image|max:10000'];

        if ($set->content_input_type !== 'upload_image') {
            $rules = [$name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000'];
        }

        CRUDBooster::valid($rules, 'view');
    }

    private function allowOnlySuperAdmin()
    {
        if (!CRUDBooster::isSuperadmin()) {
            CRUDBooster::insertLog(trans("crudbooster.log_try_view", ['name' => 'Setting', 'module' => 'Setting']));
            CRUDBooster::denyAccess();
        }
    }


    /**
     * @param $set
     * @param $name
     * @return string
     */
    private function uploadFile($set)
    {
        $this->validateFileType($set);

        $file = Request::file($set->name);
        $ext = $file->getClientOriginalExtension();

        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)) . '.' . $ext;
        if ($file->move(storage_path('app' . DIRECTORY_SEPARATOR . date('Y-m')), $filename)) {
            $content = 'uploads/' . date('Y-m') . '/' . $filename;
        }
        return $content;
    }


}
