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

class MenusGroupsController extends CBController {

    public function __construct() {
        $this->table         = "cms_menus_groups";
        $this->primary_key   = "id";
        $this->title_field   = "name";        
        $this->index_orderby = ["id"=>"desc"];

        $this->col = array();
        $this->col[] = array("label"=>"Name","name"=>"name" );
		$this->col[] = array("label"=>"Slug","name"=>"slug" );

		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name","type"=>"text"  );
		$this->form[] = array("label"=>"Slug","name"=>"slug","type"=>"text","help"=>"Enter key word without any special character, dont use space instead _ underscore" );
 
        $this->form_sub[] = ['label'=>"Menu","controller"=>"AdminCmsMenusController"];
                                                                                                               
        $this->constructor();
    }
    
}