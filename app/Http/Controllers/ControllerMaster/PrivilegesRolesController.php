<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class PrivilegesRolesController extends Controller {

	

	public function __construct() {
		$this->modulname = "Privileges Roles";
		$this->table = 'cms_privileges_roles';
		$this->primkey = 'id';
		$this->titlefield = "name";

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';		

		$this->col = array();
		$this->col[] = array("label"=>"Privileges","field"=>"name","join"=>"cms_privileges");
		$this->col[] = array("label"=>"Moduls","field"=>"name","join"=>"cms_moduls");
		$this->col[] = array("label"=>"Visible","field"=>"is_visible");		
		$this->col[] = array("label"=>"Create","field"=>"is_create");		
		$this->col[] = array("label"=>"Read","field"=>"is_read");		
		$this->col[] = array("label"=>"Update","field"=>"is_edit");		
		$this->col[] = array("label"=>"Delete","field"=>"is_delete");		

		$this->form = array();
		$this->form[] = array("label"=>"Privileges","name"=>"id_cms_privileges","type"=>"select","datatable"=>"cms_privileges,name");
		$this->form[] = array("label"=>"Moduls","name"=>"id_cms_moduls","type"=>"select","datatable"=>"cms_moduls,name");
		$this->form[] = array("label"=>"Visible","name"=>"is_visible");
		$this->form[] = array("label"=>"Create","name"=>"is_create");
		$this->form[] = array("label"=>"Read","name"=>"is_read");
		$this->form[] = array("label"=>"Update","name"=>"is_edit");
		$this->form[] = array("label"=>"Delete","name"=>"is_delete");
		$this->constructor();
	}
	
	
}
