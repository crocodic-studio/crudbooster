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

class CompaniesController extends CBController {

	public function __construct() {		
		$this->table       = 'cms_companies';
		$this->primary_key = 'id';
		$this->title_field = "name";		
	

		$this->col = array();
		$this->col[] = array("label"=>"Company Name","name"=>"name");
		$this->col[] = array("label"=>"Email","name"=>"email");
		$this->col[] = array("label"=>"Photo","name"=>"photo","image"=>true);
		$this->col[] = array("label"=>"Primary","name"=>"is_primary","callback_php"=>"(%field%)?'PRIMARY':'-'");

		$this->form = array(); 
		$this->form[] = array("label"=>"Name","name"=>"name");
		$this->form[] = array("label"=>"Email","name"=>"email");
		$this->form[] = array("label"=>"Address","name"=>"address");
		$this->form[] = array("label"=>"Phone","name"=>"phone");		
		$this->form[] = array("label"=>"Description","name"=>"description",'type'=>'textarea');
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Resolution recomended is 200x200px");
		$this->form[] = array("label"=>"Latitude","name"=>"latitude","type"=>"hidden","googlemaps"=>true);
		$this->form[] = array("label"=>"Longitude","name"=>"longitude","type"=>"hidden");
		$this->form[] = array("label"=>"Primary Company","name"=>"is_primary",'type'=>'radio','dataenum'=>array('1|Yes','0|No'));				
		
		$this->constructor();
	}
	

}
