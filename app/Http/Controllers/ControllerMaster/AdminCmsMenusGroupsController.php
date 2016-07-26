<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class AdminCmsMenusGroupsController extends Controller {

    public function __construct() {
        $this->table         = "cms_menus_groups";
        $this->primkey       = "id";
        $this->titlefield    = "name";
        $this->theme         = "admin.default"; 
        $this->prefixroute   = "admin/";
        $this->index_orderby = ["id"=>"desc"];

        $this->col = array();
        $this->col[] = array("label"=>"Name","field"=>"name" );
		$this->col[] = array("label"=>"Slug","field"=>"slug" );

		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name","type"=>"text"  );
		$this->form[] = array("label"=>"Slug","name"=>"slug","type"=>"text","help"=>"Enter key word without any special character, dont use space instead _ underscore" );
 
        $this->form_sub[] = ['label'=>"Menu","controller"=>"AdminCmsMenusController"];
                                                                                                               
        $this->constructor();
    }
    
}