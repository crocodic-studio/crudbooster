<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;

class PostsCategoriesController extends CBController {

    public function __construct() {
        $this->table         = "cms_posts_categories";
        $this->primary_key   = "id";
        $this->title_field   = "name";        
        $this->index_orderby = ["id"=>"desc"];

        $this->col   = array();
		$this->col[] = array("label"=>"Name","name"=>"name" );		

		$this->form   = array();
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
    public function hook_html_index(&$html,$data) {
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