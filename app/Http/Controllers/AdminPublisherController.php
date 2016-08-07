<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class AdminPublisherController extends Controller {

    public function __construct() {
        $this->table              = "publisher";
        $this->primkey            = "id";
        $this->titlefield         = "nama";
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
		$this->col[] = array("label"=>"Nama","field"=>"nama" );
		$this->col[] = array("label"=>"Link","field"=>"link" );
		$this->col[] = array("label"=>"Deskripsi","field"=>"deskripsi" );
		$this->col[] = array("label"=>"Image","field"=>"image" ,"image"=>true);
		$this->col[] = array("label"=>"Website","field"=>"website" );

		$this->form = array();
		$this->form[] = array("label"=>"Nama","name"=>"nama","type"=>"text"   );
		$this->form[] = array("label"=>"Link","name"=>"link","type"=>"text"   );
		$this->form[] = array("label"=>"Deskripsi","name"=>"deskripsi","type"=>"textarea"   );
		$this->form[] = array("label"=>"Image","name"=>"image","type"=>"upload"   );
		$this->form[] = array("label"=>"Website","name"=>"website","type"=>"text"   );
     
        
        //You may use this bellow array to add alert message to this module at overheader
        $this->alert        = array();
        
        //You may use this bellow array to add more your own header button 
        $this->index_button = array();            
        
        //You may use this bellow array to add relational data to next tab 
        $this->form_tab     = array();
        
        //You may use this bellow array to add relational data to next area or element, i mean under the existing form 
        $this->form_sub     = array();
        
        //You may use this bellow array to add some or more html that you want under the existing form 
        $this->form_add     = array();                                                                                      
        


        //No need chanage this constructor
        $this->constructor();
    }


    public function hook_before_index(&$result) {
        //Use this hook for manipulate query of index result 
        
    }
    public function hook_html_index(&$html_contents) {
        //Use this hook for manipulate result of html in index 
        //To get html, $html_contents["html"] 
        //To get data, $html_contents["data"]

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