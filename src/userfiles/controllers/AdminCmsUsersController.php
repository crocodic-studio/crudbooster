<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use Mail;
use Hash;
use Cache;
use Validator;
use CRUDbooster;

class AdminCmsUsersController extends \crocodicstudio\crudbooster\controllers\CBController {

	public function __construct() {
		$this->module_name         = "User";
		$this->table               = 'cms_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';	
		$this->button_import 	   = false;	
		$this->button_export 	   = false;	
	

		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Privilege","name"=>"id_cms_privileges","join"=>"cms_privileges,name");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>1);		

		$this->form = array(); 
		$this->form[] = array("label"=>"Primary Data","type"=>"header");
		$this->form[] = array("label"=>"Name","name"=>"name",'required'=>true,'validation'=>'required|alpha_spaces|min:3');
		$this->form[] = array("label"=>"Email","name"=>"email",'required'=>true,'type'=>'email','validation'=>'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId());		
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Resolution recomended is 200x200px",'required'=>true,'validation'=>'required|image|max:1000');								
		if(CRUDBooster::getCurrentMethod() != 'getProfile') {			
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'required'=>true);		
		}
		$this->form[] = array("label"=>"Change the Password","type"=>"header",'collapsed'=>false);
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not change");

		if(CRUDBooster::getCurrentMethod() == 'getProfile') {
			$this->button_addmore = false;
			$this->button_cancel  = false;
			$this->button_show    = false;			
			$this->button_add     = false;
			$this->button_delete  = false;			
		}
		
		
		$this->constructor();
	}

	public function getProfile() {				
		$data['page_title'] = trans("crudbooster.label_button_profile");
		$data['row']        = DB::table($this->table)->where($this->primary_key,CRUDBooster::myId())->first();
		$data['return_url'] = Request::fullUrl();
		return view('crudbooster::default.form',$data);
	}
}
