<?php

namespace Crocodicstudio\Crudbooster\Http\Controllers;

use Crocodicstudio\Crudbooster\Http\Controllers\Controller;
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
use CB;

class CBSettingsController extends CBController
{
	public function cbInit()
	{		
		$this->table        = 'cb_settings';
		$this->primaryKey   = 'id';
		$this->titleField   = 'name';
		$this->orderBy      = 'name,asc';
		$this->buttonDelete = true;
		$this->buttonShow   = false;
		$this->buttonCancel = false;
		$this->buttonImport = false;
		$this->buttonExport = false;

		$this->columns = [];
		$this->columns[] = ["label"=>"Label","name"=>"label"];
		$this->columns[] = ["label"=>"Setting","name"=>"content"];

		$this->inputs = [];

		if (Request::get('group_setting')) {
			$value = Request::get('group_setting');
		} else {
			$value = 'General Setting';
		}

		$this->inputs[] = array('label'=>'Group','name'=>'group_setting','value'=>$value);
		$this->inputs[] = array('label'=>'Label','name'=>'label');
		$this->inputs[] = array("label"=>"Type","name"=>"content_input_type","type"=>"select","dataenum"=>array("text","number","email","textarea","wysiwyg","upload_image","upload_document","datepicker","radio","select"));		
		$this->inputs[] = array("label"=>"Radio / Select Data","name"=>"dataenum","placeholder"=>"Example : abc,def,ghi");
		$this->inputs[] = array("label"=>"Helper Text","name"=>"helper","type"=>"text");

		$this->scriptJS = "
		function show_radio_data() {
				var cit = $('#content_input_type').val();
				if(cit == 'radio' || cit == 'select') {
					$('#form-group-dataenum').show();	
				}else{
					$('#form-group-dataenum').hide();
				}					
		}
		$(function() {
			$('#content_input_type').change(show_radio_data);
			show_radio_data();	
		})		
		";
	}

	function getShow()
	{		
		if (!CB::isSuperadmin()) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>'Setting','module'=>'Setting']));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$data['pageTitle'] = urldecode(Request::get('group'));
		return view('CB::setting',$data);
	} 

	function hookBeforeEdit(&$postData,$id)
	{
		$this->returnURL = CB::mainpath("show")."?group=".$postData['group_setting'];
	}

	function getDeleteFileSetting()
	{
		$id = g('id');
		$row = CB::first('cb_settings',$id);
		if(Storage::exists($row->content)) Storage::delete($row->content);
		DB::table('cb_settings')->where('id',$id)->update(['content'=>NULL]);
		CB::redirect(Request::server('HTTP_REFERER'),trans('alert_delete_data_success'),'success');	
	}	

	function postSaveSetting()
	{

		if (!CB::isSuperadmin()) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>'Setting','module'=>'Setting']));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$group = Request::get('group_setting');
		$setting = DB::table('cb_settings')->where('group_setting',$group)->get();
		foreach($setting as $set) {			

			$name = $set->name;
			$content = Request::get($set->name);
			if (Request::hasFile($name)) {

				if ($set->content_input_type == 'upload_image') {
					CB::valid([ $name => 'image|max:10000' ],'view');
				} else {
					CB::valid([ $name => 'mimes:doc,docx,xls,xlsx,ppt,pptx,pdf,zip,rar|max:20000' ], 'view');
				}

				$file = Request::file($name);					
				$ext  = $file->getClientOriginalExtension();

				//Create Directory Monthly 
				Storage::makeDirectory(date('Y-m'));

				//Move file to storage
				$fileName = md5(str_random(5)).'.'.$ext;
				if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$fileName)) {
					$content = 'uploads/'.date('Y-m').'/'.$fileName;
				}
			}

			DB::table('cms_settings')->where('name',$set->name)->update(['content'=>$content]);

			Cache::forget('setting_'.$set->name);
		}

		return redirect()->back()->with(['message'=>'Your setting has been saved !','message_type'=>'success']);
	}

	function hookBeforeAdd(&$postData)
	{
		$arr['name'] = str_slug($postData['label'],'_');
		$this->returnURL = CB::mainpath("show")."?group=".$postData['group_setting'];
	}

	function hookAfterEdit($id)
	{		
		$row = CB::first($this->table,$id);		
		Cache::forget('setting_'.$row->name);
	}

}
