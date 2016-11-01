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

class SettingsController extends CBController {

	public function __construct() {
		$this->module_name   = "Settings";
		$this->table         = 'cms_settings';
		$this->primary_key   = 'id';
		$this->title_field   = "name";		
		$this->index_orderby = array('name'=>'asc');
		$this->button_delete_data = false;
		$this->button_show_data = false;

		$this->col = array();
		$this->col[] = array("label"=>"Nama","name"=>"name","callback_php"=>"ucwords(str_replace('_',' ',%field%))");
		$this->col[] = array("label"=>"Setting","name"=>"content");

		$this->form = array();
		
		
		if(Request::get('group_setting')) {
			$value = Request::get('group_setting');
		}else{
			$value = 'General Setting';
		}

		$this->form[] = array('label'=>'Group','name'=>'group_setting','value'=>$value);
		$this->form[] = array('label'=>'Label','name'=>'label');
		// $this->form[] = array("label"=>"Name","name"=>"name","help"=>"Without space, special char, sparate with _ , lowercase only");
		$this->form[] = array("label"=>"Type","name"=>"content_input_type","type"=>"select","dataenum"=>array("text","number","email","textarea","wysiwyg","upload_image","upload_document","datepicker","radio","select"));		
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
		
		$this->constructor();
	}

	function getIndex() {
		$data['page_title']      = $this->data['module_name'];
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();	
		$data['table_name']      = $this->table;		
		$data['controller_name'] = $this->controller_name;

		if(g('group')) {
			$data['group_setting']   = DB::table('cms_settings')->where('group_setting',Request::get('group'))->lists('group_setting');
		}else{
			$data['group_setting']   = DB::table('cms_settings')->groupby('group_setting')->lists('group_setting');
		}		
		return view('crudbooster::default.setting',$data);
	} 

	function getDeleteFileSetting() {
		$id = g('id');
		$row = first_row('cms_settings',$id);
		if(Storage::exists($row->content)) Storage::delete($row->content);
		DB::table('cms_settings')->where('id',$id)->update(['content'=>NULL]);
		return redirect()->back()->with(['message'=>'Delete file successfully !','message_type'=>'success']);
	}	


	function postSaveSetting() {
		$group = Request::get('group_setting');
		$setting = DB::table('cms_settings')->where('group_setting',$group)->get();
		foreach($setting as $set) {
			
			$name = $set->name;

			$content = Request::get($set->name);

			if (Request::hasFile($name))
			{			

				if($set->content_input_type == 'upload_image') {
					valid([ $name => 'image|max:10000' ],'view');
				}else{
					valid([ $name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000' ], 'view');
				}


				$file = Request::file($name);					
				$ext  = $file->getClientOriginalExtension();

				//Create Directory Monthly 
				Storage::makeDirectory(date('Y-m'));

				//Move file to storage
				$filename = md5(str_random(5)).'.'.$ext;
				if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {						
					$content = 'uploads/'.date('Y-m').'/'.$filename;
				}					  
			}


			DB::table('cms_settings')->where('name',$set->name)->update(['content'=>$content]);

			Cache::forget('setting_'.$set->name);
		}
		return redirect()->back()->with(['message'=>'Your setting has been saved !','message_type'=>'success']);
	}

	function hook_before_add(&$arr) {
		$arr['name'] = str_slug($arr['label'],'_');
	}

	function hook_after_edit($id) {
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		/* REMOVE CACHE */
		Cache::forget('setting_'.$row->name);
	}
	

}
