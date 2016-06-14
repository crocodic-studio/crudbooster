<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;
//use App\Produk;

class ModulsGroupController extends Controller {

		

	public function __construct() {
		$this->modulname = "Modul Group";
		$this->table = 'cms_moduls_group';
		$this->primkey = 'id';
		$this->titlefield = "nama_group";

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';	

		$this->col = array();				
		$this->col[] = array("label"=>"Sorting","field"=>"sorting_group");
		$this->col[] = array("label"=>"Nama","field"=>"nama_group");
		$this->col[] = array("label"=>"Is Group","field"=>"is_group");

		$this->form = array();		
		$this->form[] = array("label"=>"Nama","name"=>"nama_group");
		$this->form[] = array("label"=>"Icon","name"=>"icon_group","type"=>"radio","dataenum"=>array(
				"fa fa-cog|<i class='fa fa-cog'></i>",
				"fa fa-comment|<i class='fa fa-comment'></i>",
				"fa fa-users|<i class='fa fa-users'></i>",
				"fa fa-file|<i class='fa fa-file'></i>",
				"fa fa-database|<i class='fa fa-database'></i>",
				"fa fa-bank|<i class='fa fa-bank'></i>",
				"fa fa-bars|<i class='fa fa-bars'></i>",
				"fa fa-check-square|<i class='fa fa-check-square'></i>",
				"fa fa-car|<i class='fa fa-car'></i>",
				"fa fa-eye|<i class='fa fa-eye'></i>",
				"fa fa-area-chart|<i class='fa fa-area-chart'></i>",
				"fa fa-envelope|<i class='fa fa-envelope'></i>",
				"fa fa-tag|<i class='fa fa-tag'></i>",
				"fa fa-truck|<i class='fa fa-truck'></i>",
				"fa fa-trash|<i class='fa fa-trash'></i>",
				"fa fa-tasks|<i class='fa fa-tasks'></i>",
				"fa fa-cube|<i class='fa fa-cube'></i>",
				"fa fa-hourglass|<i class='fa fa-hourglass'></i>",
				"fa fa-dashboard|<i class='fa fa-dashboard'></i>",
				"fa fa-flag-o|<i class='fa fa-flag-o'></i>",
				"fa fa-folder-open|<i class='fa fa-folder-open'></i>",
				"fa fa-credit-card|<i class='fa fa-credit-card'></i>",
				"fa fa-inbox|<i class='fa fa-inbox'></i>",
				"fa fa-spinner|<i class='fa fa-spinner'></i>",
				"fa fa-line-cart|<i class='fa fa-line-cart'></i>",
				"fa fa-info-circle|<i class='fa fa-info-circle'></i>",
				"fa fa-cloud-download|<i class='fa fa-cloud-download'></i>",
			));
		$this->form[] = array("label"=>"Sorting","name"=>"sorting_group");
		$this->form[] = array("label"=>"Is Group","name"=>"is_group","type"=>"radio","dataenum"=>array("0|Tidak","1|Ya"));
		
		$this->constructor();
	}

}
