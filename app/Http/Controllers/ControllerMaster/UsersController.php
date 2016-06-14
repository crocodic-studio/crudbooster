<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;
//use App\Users;

class UsersController extends Controller {

	public function __construct() {
		$this->modulname = "User";
		$this->table = 'cms_users';
		$this->primkey = 'id';
		$this->titlefield = "name";

		$this->theme = 'admin.default';	 
		$this->prefixroute = 'admin/';	
	

		$this->col = array();
		$this->col[] = array("label"=>"Name","field"=>"name");
		$this->col[] = array("label"=>"Email","field"=>"email");
		$this->col[] = array("label"=>"Privilege","field"=>"name","join"=>"cms_privileges");
		$this->col[] = array("label"=>"Photo","field"=>"photo","image"=>1);
		$this->col[] = array("label"=>"Company","field"=>"name","join"=>'cms_companies');

		$this->form = array(); 
		$this->form[] = array("label"=>"Name","name"=>"name");
		$this->form[] = array("label"=>"Email","name"=>"email");
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not change");
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Resolution recomended is 200x200px");
		
		
		if(strtolower(Session::get('admin_privileges_name'))=='superadmin') {
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name");
			#$this->form[] = array("label"=>"Company","name"=>"id_cms_companies","type"=>"select","datatable"=>"cms_companies,name");			
			$this->form[] = array("label"=>"Company","name"=>"id_cms_companies","type"=>"browse","browse_source"=>"CompaniesController","browse_columns"=>(new CompaniesController)->get_cols());
		}else{
			$this->form[] = array("label"=>"Privilege","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name",'datatable_exception'=>'Superadmin');
			$this->form[] = array("label"=>"Company","name"=>"id_cms_companies","type"=>"hide","value"=>Session::get('filter_value'));
		}
		
		
		$this->constructor();
	}
}
