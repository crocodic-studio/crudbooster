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

class MenusController extends CBController {

    public function __construct() {
        $this->table         = "cms_menus";
        $this->primary_key   = "id";
        $this->title_field   = "name";        
        $this->index_orderby = ["parent_id_cms_menus"=>"asc"];

        $this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name" );
		$this->col[] = array("label"=>"Parent","name"=>"parent_id_cms_menus","join"=>"cms_menus,name","callback_php"=>'($row->name_cms_menus1=="")?"<span class=\"label label-info\">PARENT</span>":$row->name_cms_menus1');					
		$this->col[] = array("label"=>"Menu Type","name"=>"menu_type" );		 		

		$this->form = array();				
		$this->form[] = array("name"=>"id_cms_menus_groups","type"=>"hidden"  );		
		$this->form[] = array("label"=>"Name","required"=>1,"name"=>"name","type"=>"text"  );		
		$this->form[] = array("label"=>"Menu Type","required"=>1,"name"=>"menu_type","type"=>"select","dataenum"=>array("Custom Link","Posts","Categories","Pages"),"value"=>"Custom Link","jquery"=>"
			function show_advanced_menu() {
				var t = $('#menu_type').val();
				$('#form-group-menu_link').hide();
				$('#form-group-id_cms_posts').hide();
				$('#form-group-id_cms_pages').hide();
				$('#form-group-id_cms_posts_categories').hide();
				switch(t) {
					case 'Custom Link':
						$('#form-group-menu_link').show();
					break;
					case 'Posts':
						$('#form-group-id_cms_posts').show();
					break;
					case 'Categories':
						$('#form-group-id_cms_posts_categories').show();
					break;
					case 'Pages':
						$('#form-group-id_cms_pages').show();
					break;
				}
			}
			show_advanced_menu();
			$('#menu_type').change(show_advanced_menu);

			" );
		$this->form[] = array("label"=>"Link","name"=>"menu_link","type"=>"text"  );
		$this->form[] = array("label"=>"Pages","name"=>"id_cms_pages","type"=>"select" ,"datatable"=>"cms_pages,title" );
		$this->form[] = array("label"=>"Posts","name"=>"id_cms_posts","type"=>"select" ,"datatable"=>"cms_posts,title" );
		$this->form[] = array("label"=>"Posts Categories","name"=>"id_cms_posts_categories","type"=>"select" ,"datatable"=>"cms_posts_categories,name" );
		$this->form[] = array("label"=>"Parent Menu","name"=>"parent_id_cms_menus","type"=>"select","datatable"=>"cms_menus,name","help"=>"Please leave default if you want make as parent menu" );
                                                                                                       
        
        $this->constructor();
    }
    
}