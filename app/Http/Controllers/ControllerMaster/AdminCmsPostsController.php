<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class AdminCmsPostsController extends Controller {

    public function __construct() {
		$this->table              = "cms_posts";
		$this->primkey            = "id";
		$this->titlefield         = "title";
		$this->theme              = "admin.default"; 
		$this->prefixroute        = "admin/";
		$this->index_orderby      = ["id"=>"desc"];
		$this->button_show_data   = true;
		$this->button_reload_data = true;
		$this->button_new_data    = true;
		$this->button_delete_data = true;
		$this->button_sort_data   = true;
		$this->button_filter_data = true;
		$this->button_export_data = true;

        $this->col = array();
        $this->col[] = array("label"=>"Title","field"=>"title" );
		$this->col[] = array("label"=>"Created","field"=>"created_at" );
		$this->col[] = array("label"=>"Content","field"=>"content","str_limit"=>155);
		$this->col[] = array("label"=>"Author","field"=>"id_cms_users","join"=>"cms_users,name");		
		$this->col[] = array("label"=>"Category","field"=>"id_cms_posts_categories","join"=>"cms_posts_categories,name");

		$this->form = array();
        $this->form[] = array("name"=>"slug","type"=>"hidden");
		$this->form[] = array("label"=>"Title","name"=>"title","type"=>"text"  );
		$this->form[] = array("label"=>"Content","name"=>"content","type"=>"wysiwyg"  );
		$this->form[] = array("label"=>"Author","name"=>"id_cms_users","type"=>"select" ,"datatable"=>"cms_users,name" );		
		$this->form[] = array("label"=>"Category","name"=>"id_cms_posts_categories","type"=>"select" ,"datatable"=>"cms_posts_categories,name" );

		// You may use this bellow array to add alert message to this module at overheader		
		$this->alert        = array();
		
		// You may use this bellow array to add more your own header button 		
		$this->index_button = array();		 		             
		
		// You may use this bellow array to add more your own row button in addition to View,Edit,Delete		
		$this->addaction    = array();

		// You may use this bellow array to add relational data to next tab 
		$this->form_tab     = array();
		
		// You may use this bellow array to add relational data to next area or element, i mean under the existing form 
		$this->form_sub     = array();
		
		// You may use this bellow array to add some or more html that you want under the existing form 
		$this->form_add     = array();                                                                                      
        


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