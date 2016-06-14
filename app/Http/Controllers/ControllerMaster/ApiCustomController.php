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

class ApiCustomController extends Controller {

	

	public function __construct() {
		$this->modulname = "Api Custom List";
		$this->table = 'cms_apicustom';
		$this->primkey = 'id';
		$this->titlefield = "nama";

		$this->theme = 'admin.default';	 
		$this->prefixroute = 'admin/';
	

		$this->col = array();
		$this->col[] = array("label"=>"Nama","field"=>"nama");

		$this->form = array(); 
		$this->form[] = array("label"=>"Nama","name"=>"nama");
		$this->form[] = array("label"=>"Permalink","name"=>"permalink",'readonly'=>true);
		$this->form[] = array("label"=>"Aksi","name"=>"aksi");
		$this->form[] = array("label"=>"kolom","name"=>"kolom",'type'=>'textarea');
		$this->form[] = array("label"=>"Sub Query","name"=>"sub_query_1",'type'=>'textarea');
		$this->form[] = array("label"=>"SQL Where","name"=>"sql_where",'type'=>'textarea');
		$this->form[] = array("label"=>"Keterangan","name"=>"keterangan",'type'=>'textarea');
		$this->form[] = array("label"=>"Parameter","name"=>"parameter",'type'=>'text');				
		
		$this->constructor();
	}
	

}
