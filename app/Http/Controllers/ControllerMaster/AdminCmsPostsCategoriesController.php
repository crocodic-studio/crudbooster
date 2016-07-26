<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class AdminCmsPostsCategoriesController extends Controller {

    public function __construct() {
        $this->table         = "cms_posts_categories";
        $this->primkey       = "id";
        $this->titlefield    = "name";
        $this->theme         = "admin.default"; 
        $this->prefixroute   = "admin/";
        $this->index_orderby = ["id"=>"desc"];

        $this->col = array();
		$this->col[] = array("label"=>"Name","field"=>"name" );		

		$this->form = array();
        $this->form[] = array("name"=>"slug","type"=>"hidden");
		$this->form[] = array("label"=>"Name","name"=>"name","type"=>"text"  );		
                 
        
        //You may use this bellow array to add relational data to next tab 
        $this->form_tab = array();

        //You may use this bellow array to add relational data to next area or element, i mean under the existing form 
        $this->form_sub = array();

        //You may use this bellow array to add some or more html that you want under the existing form 
        $this->form_add = array();                                                                                      
        


        //No need chanage this constructor
        $this->constructor();
    }


    public function hook_before_index(&$result) {
        //Use this hook for manipulate query of index result 
        
    }
    public function hook_html_index(&$html_contents) {
        //Use this hook for manipulate result of html in index 

    }
    public function hook_before_add(&$arr) {
        //Use this hook for manipulate data input before add data is execute 

    }
    public function hook_after_add($id) {
        //Use this hook if you want execute other command after add function called 

    }
    public function hook_before_edit(&$arr,$id) {
        //Use this hook for manipulate data input before update data is execute 

    }
    public function hook_after_edit($id) {
        //Use this hook if you want execute other command after update data called 

    }
    public function hook_before_delete($id) {
        //Use this hook if you want execute other command before delete command called 

    }
    public function hook_after_delete($id) {
        //Use this hook if you want execute other command after delete command called 

    }
    
}