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

class UsersController extends CBController {

	public function __construct() {
		$this->module_name = "User";
		$this->table       = 'cms_users';
		$this->primary_key = 'id';
		$this->title_field = "name";		
	

		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);
		$this->col[] = array("label"=>"Company","name"=>"id_cms_companies","join"=>'cms_companies,name');

		$this->form = array(); 
		$this->form[] = array("label"=>"Primary Data","type"=>"header");
		$this->form[] = array("label"=>"Name","name"=>"name",'required'=>true,'validation'=>'required|alpha_spaces|min:3');
		$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users');		
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload_standard",'upload_file'=>false,"help"=>"Resolution recomended is 200x200px",'required'=>true,'validation'=>'required|image|max:1000');								
		if(get_method() == 'getEdit' || get_method() == 'getAdd') {
			$this->form[] = array("label"=>"Company","name"=>"id_cms_companies","type"=>"select","datatable"=>"cms_companies,name");				
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name");		
		}
		$this->form[] = array("label"=>"Change the Password","type"=>"header");
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not change");

		if(get_method() == 'getProfile') {
			$this->button_addmore     = FALSE;
			$this->button_cancel      = FALSE;
			$this->button_show_data   = false;
			$this->button_reload_data = false;
			$this->button_new_data    = false;
			$this->button_delete_data = false;			
		}
		
		
		$this->constructor();
	}

	public function getProfile() {
		$id = get_my_id();
		$data['page_title'] = "My Profile";
		$data['row'] = DB::table($this->table)->where($this->primary_key,$id)->first();
		return view('crudbooster::default.form',$data);
	}
}
