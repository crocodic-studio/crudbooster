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

class LogsController extends Controller {

	public function __construct() {
		$this->modulname = "Logs";
		$this->table = 'cms_logs';
		$this->primkey = 'id';
		$this->titlefield = "ipaddress";

		$this->theme = 'admin.default';	 
		$this->prefixroute = 'admin/';	
	

		$this->col = array();
		$this->col[] = array("label"=>"Created At","field"=>"created_at");
		$this->col[] = array("label"=>"IP Address","field"=>"ipaddress");	
		$this->col[] = array("label"=>"User","field"=>"id_cms_users","join"=>"cms_users,name");	
		$this->col[] = array("label"=>"Description","field"=>"description");		
		
		$this->form = array(); 	
		$this->form[] = array("label"=>"Created At","name"=>"created_at","readonly"=>true);
		$this->form[] = array("label"=>"IP Address","name"=>"ipaddress","readonly"=>true);	
		$this->form[] = array("label"=>"URL","name"=>"url","readonly"=>true);	
		$this->form[] = array("label"=>"User","name"=>"id_cms_users","type"=>"select","datatable"=>"cms_users,name","readonly"=>true);	
		$this->form[] = array("label"=>"Description","name"=>"description","readonly"=>true);	
		
		$this->constructor();
	}
	

}
