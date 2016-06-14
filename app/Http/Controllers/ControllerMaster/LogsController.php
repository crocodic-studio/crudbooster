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
		$this->col[] = array("label"=>"UserAgent","field"=>"useragent");
		$this->col[] = array("label"=>"URL","field"=>"url");
		$this->col[] = array("label"=>"User","field"=>"name","join"=>"cms_users");
		
		$this->form = array(); 		
		
		$this->constructor();
	}
	

}
