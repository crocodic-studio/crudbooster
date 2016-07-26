<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;
//use App\Users;

class SettingsController extends Controller {

	

	public function __construct() {
		$this->modulname = "Settings";
		$this->table = 'cms_settings';
		$this->primkey = 'id';
		$this->titlefield = "name";

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';		
		$this->index_orderby = array('name'=>'asc');

		$this->col = array();
		$this->col[] = array("label"=>"Nama","field"=>"name","callback_php"=>"ucwords(str_replace('_',' ',%field%))");
		$this->col[] = array("label"=>"Setting","field"=>"content");

		$this->form = array();
		
		
		$id = Request::segment(4);
		$id = intval($id);
		
		if(is_int($id) && $id !=0) {
			$ro = DB::table('cms_settings')->where('id',$id)->first();
			$type = $ro->content_input_type;

			$this->form[] = array("label"=>"Name","name"=>"name","readonly"=>true,"callback_php"=>'ucwords(str_replace("_"," ",$row->name))');

			if($type=='radio' || $type=='select') {
				if($ro->dataenum) {
					$dataenum = explode(",",$ro->dataenum);					
					$this->form[] = ["label"=>"Content","type"=>$type,"name"=>"content","dataenum"=>$dataenum,"help"=>$ro->helper];
				}	
			}else{
				$this->form[] = array("label"=>"Content","name"=>"content","type"=>$type,"help"=>$ro->helper);	
			}
						
		}else{			
			$this->form[] = array("label"=>"Name","name"=>"name","help"=>"Without space, special char, sparate with _ , lowercase only");

			$this->form[] = array("label"=>"Type","name"=>"content_input_type","type"=>"select","dataenum"=>array("text","textarea","wysiwyg","upload","datepicker","radio","select"));		
			$this->form[] = array("label"=>"Radio / Select Data","name"=>"dataenum","placeholder"=>"Example : abc,def,ghi","jquery"=>"
				function show_radio_data() {
					var cit = $('#content_input_type').val();
					if(cit == 'radio' || cit == 'select') {
						$('#form-group-dataenum').show();	
					}else{
						$('#form-group-dataenum').hide();
					}					
				}
				$('#content_input_type').change(show_radio_data);
				show_radio_data();
				");
			$this->form[] = array("label"=>"Helper Text","name"=>"helper","type"=>"text");	
		}				
		
		$this->constructor();
	}
	

}
