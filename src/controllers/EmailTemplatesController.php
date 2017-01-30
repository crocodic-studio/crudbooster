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
	use CRUDBooster;

	class EmailTemplatesController extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {
	        $this->table              = "cms_email_templates";
	        $this->primary_key        = "id";
	        $this->title_field        = "name";
	        $this->limit              = 20;
	        $this->orderby      	  = ["id"=>"desc"];
	        $this->global_privilege   = FALSE;
	        
			$this->button_table_action = TRUE;   
			$this->button_action_style = "button_icon";     
			$this->button_add          = TRUE;
			$this->button_delete       = TRUE;
			$this->button_edit         = TRUE;
			$this->button_detail       = TRUE;
			$this->button_show         = TRUE;
			$this->button_filter       = TRUE;        
			$this->button_export       = FALSE;	        
			$this->button_import       = FALSE;							      

	        $this->col = array();
			$this->col[] = array("label"=>"Template Name","name"=>"name" );
			$this->col[] = array("label"=>"Slug","name"=>"slug" );

			$this->form = array();
			$this->form[] = array("label"=>"Template Name","name"=>"name","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255|alpha_spaces","placeholder"=>"You can only enter the letter only");
			$this->form[] = array("label"=>"Slug","type"=>"text","name"=>"slug","required"=>true,'validation'=>'required|unique:cms_email_templates,slug');
			$this->form[] = array("label"=>"Subject","name"=>"subject","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255");
			$this->form[] = array("label"=>"Content","name"=>"content","type"=>"wysiwyg","required"=>TRUE,"validation"=>"required");
			$this->form[] = array("label"=>"Description","name"=>"description","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255");
			
			$this->form[] = array("label"=>"From Name","name"=>"from_name","type"=>"text","required"=>false,"width"=>"col-sm-6",'placeholder'=>'Optional');
			$this->form[] = array("label"=>"From Email","name"=>"from_email","type"=>"email","required"=>false,"validation"=>"email","width"=>"col-sm-6",'placeholder'=>'Optional');
			
			$this->form[] = array("label"=>"Cc Email","name"=>"cc_email","type"=>"email","required"=>false,"validation"=>"email",'placeholder'=>'Optional');
	        
	    }


	
	    //By the way, you can still create your own method in here... :) 


	}
