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

class CompaniesController extends Controller {

	public function __construct() {
		$this->modulname = "Companies";
		$this->table = 'cms_companies';
		$this->primkey = 'id';
		$this->titlefield = "name";

		$this->theme = 'admin.default';	 
		$this->prefixroute = 'admin/';	
	

		$this->col = array();
		$this->col[] = array("label"=>"Company Name","field"=>"name");
		$this->col[] = array("label"=>"Email","field"=>"email");
		$this->col[] = array("label"=>"Photo","field"=>"photo","image"=>true);
		$this->col[] = array("label"=>"Primary","field"=>"is_primary","callback_php"=>"(%field%)?'PRIMARY':'-'");

		$this->form = array(); 
		$this->form[] = array("label"=>"Name","name"=>"name");
		$this->form[] = array("label"=>"Email","name"=>"email");
		$this->form[] = array("label"=>"Address","name"=>"address");
		$this->form[] = array("label"=>"Phone","name"=>"phone");
		$this->form[] = array("label"=>"Description","name"=>"description",'type'=>'textarea');
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Resolution recomended is 200x200px");
		$this->form[] = array("label"=>"Primary Company","name"=>"is_primary",'type'=>'radio','dataenum'=>array('1|Yes','0|No'));
		
		
		$this->constructor();
	}
	

}
