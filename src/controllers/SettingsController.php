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

		$this->col = array();
		$this->col[] = array("label"=>"Nama","name"=>"name","callback_php"=>"ucwords(str_replace('_',' ',%field%))");
		$this->col[] = array("label"=>"Setting","name"=>"content");

		$this->form = array();
		
		
		$id = Request::segment(4);
		$id = intval($id);
		
		if(is_int($id) && $id !=0) {
			$ro = DB::table('cms_settings')->where('id',$id)->first();
			$type = $ro->content_input_type;

			$this->form[] = array("label"=>"Name","name"=>"name","readonly"=>true,"callback_php"=>'ucwords(str_replace("_"," ",$row->name))');

			if($type=='radio' || $type=='select') {
				if($ro->dataenum) {
					$dataenum = explode(",",$ro->dataenum);					
					$this->form[] = ["label"=>"Content","type"=>$type,"name"=>"content","dataenum"=>$dataenum,"help"=>$ro->helper];
				}	
			}else{
				$this->form[] = array("label"=>"Content","name"=>"content","type"=>$type,"help"=>$ro->helper);	
			}
						
		}else{			
			$this->form[] = array("label"=>"Name","name"=>"name","help"=>"Without space, special char, sparate with _ , lowercase only");

			$this->form[] = array("label"=>"Type","name"=>"content_input_type","type"=>"select","dataenum"=>array("text","textarea","wysiwyg","upload","datepicker","radio","select"));		
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
		
		$this->constructor();
	}

	function hook_after_edit($id) {
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		/* REMOVE CACHE */
		Cache::forget('setting_'.$row->name);
	}
	

}
