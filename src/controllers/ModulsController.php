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

class ModulsController extends CBController {
	
	public function __construct() {		
		$this->table       = 'cms_moduls';
		$this->primary_key = 'id';
		$this->title_field = "name";
		$this->limit       = 100;

		
		$this->col = array();
		$this->col[] = array('label'=>'Sorting','name'=>'sorting');
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Table","name"=>"table_name");		
		$this->col[] = array("label"=>"Path","name"=>"path"); 		
		$this->col[] = array("label"=>"Controller","name"=>"controller");
		$this->col[] = array("label"=>"Group","name"=>"id_cms_moduls_group","join"=>"cms_moduls_group,nama_group");		
		$this->col[] = array("label"=>"Active","name"=>"is_active","callback_php"=>"(%field%)?'<span class=\"label label-success\">Active</span>':'<span class=\"label label-default\">Not Active</span>'");		

		$this->form = array();	
		$this->form[] = ['label'=>'Basic Configuration','type'=>'header'];	
		$this->form[] = array("label"=>"Name","name"=>"name","placeholder"=>"Enter a module name",);

		$tables = list_tables();
		$tables_list = array();		
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = $value;	
				if(is_exists_controller($value)) {
					$label = $label." [Already]";
				}
				
				if(substr($value, 0,4)=='cms_') continue;

				$tables_list[] = $value."|".$label;
			}
		}
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = "[Default] ".$value;	
				if(substr($value, 0,4)=='cms_') $tables_list[] = $value."|".$label;				
			}
		}



		$this->form[] = array("label"=>"Table Name","name"=>"table_name","type"=>"select","dataenum"=>$tables_list);


		$this->form[] = ['label'=>'Advanced Configuration','type'=>'header',"collapsed"=>false];
		$this->form[] = array("label"=>"Icon","name"=>"icon","type"=>"radio","dataenum"=>array(
				"fa fa-bars|<i class='fa fa-bars'></i>",
				"fa fa-cog|<i class='fa fa-cog'></i>",
				"fa fa-comment|<i class='fa fa-comment'></i>",
				"fa fa-users|<i class='fa fa-users'></i>",
				"fa fa-file|<i class='fa fa-file'></i>",
				"fa fa-database|<i class='fa fa-database'></i>",
				"fa fa-bank|<i class='fa fa-bank'></i>",				
				"fa fa-check-square|<i class='fa fa-check-square'></i>",
				"fa fa-car|<i class='fa fa-car'></i>",
				"fa fa-eye|<i class='fa fa-eye'></i>",
				"fa fa-area-chart|<i class='fa fa-area-chart'></i>",
				"fa fa-envelope|<i class='fa fa-envelope'></i>",
				"fa fa-tag|<i class='fa fa-tag'></i>",
				"fa fa-truck|<i class='fa fa-truck'></i>",
				"fa fa-trash|<i class='fa fa-trash'></i>",
				"fa fa-tasks|<i class='fa fa-tasks'></i>",
				"fa fa-cube|<i class='fa fa-cube'></i>",				
				"fa fa-dashboard|<i class='fa fa-dashboard'></i>",				
				"fa fa-folder-open|<i class='fa fa-folder-open'></i>",
				"fa fa-credit-card|<i class='fa fa-credit-card'></i>",
				"fa fa-inbox|<i class='fa fa-inbox'></i>",								
				"fa fa-info-circle|<i class='fa fa-info-circle'></i>",
				"fa fa-cloud-download|<i class='fa fa-cloud-download'></i>",
			),"value"=>"fa fa-bars");			
 

		$this->form[] = array("label"=>"Route","name"=>"path","jquery"=>"
			$('#table_name').change(function() {
				var v = $(this).val();
				$('#path').val(v);
			})
			");
		$this->form[] = array("label"=>"Controller","name"=>"controller","type"=>"text","placeholder"=>"Auto Generated");
		$this->form[] = array("label"=>"SQL Where Query","name"=>"sql_where","type"=>"text","placeholder"=>"Example : columnname = value AND columnname2 = value2","help"=>"You can use alias [admin_id],[admin_id_companies]");
		$this->form[] = array("label"=>"SQL Order By","name"=>"sql_orderby","type"=>"text","placeholder"=>"Enter query here","help"=>"Example : column_name ASC, column2_name DESC");
		$this->form[] = ['label'=>"Limit Data","name"=>"limit_data","type"=>"text","placeholder"=>"Example : 10"];
		$this->form[] = array("label"=>"Delete Data Mode","name"=>"is_softdelete","type"=>"radio","dataenum"=>['1|Soft Delete','0|Permanent Delete'],"help"=>"Soft Delete Note : Please make sure you have column 'deleted_at' with data type TIMESTAMP","value"=>0);				
		$this->form[] = array("label"=>"Group","name"=>"id_cms_moduls_group","required"=>"required","type"=>"select","datatable"=>"cms_moduls_group,nama_group","visible"=>true,'value'=>Request::segment(4));		
		$this->form[] = array("label"=>"Active ?","help"=>"Active to visible at sidebar menu, Not Active to unvisible","name"=>"is_active","type"=>"select","dataenum"=>array("1|Active","0|Not Active"),"value"=>1);
		
		$url_find_sorting = url(config('crudbooster.ADMIN_PATH').'/cms_moduls/find-last-sorting').'/'.Request::segment(4);
		
		$this->form[] = array("label"=>"Sorting","name"=>"sorting","jquery"=>"				
				$.get('$url_find_sorting',function(resp) {
					resp = parseInt(resp) + 1;
					$('#sorting').val(resp);
				});
			","help"=>"Integer/Number");				
		

		$this->addaction = array();				
		$this->addaction[] = array('label'=>'Up','route'=>url(config('crudbooster.ADMIN_PATH')).'/cms_moduls/arr-sorting/%id%/up','icon'=>'fa fa-arrow-up','ajax'=>true);
		$this->addaction[] = array('label'=>'Down','route'=>url(config('crudbooster.ADMIN_PATH')).'/cms_moduls/arr-sorting/%id%/down','icon'=>'fa fa-arrow-down','ajax'=>true);
		
		$this->index_orderby = array("id_cms_moduls_group"=>"asc","sorting"=>"asc");

		$this->constructor();
	}

	function hook_before_delete($id) {
		$modul = DB::table('cms_moduls')->where('id',$id)->first();
		@unlink(app_path('Http/Controllers/Admin'.$modul->controller.'.php'));		
	}

	public function getFindLastSorting($id_moduls_group) {
		$ro = DB::table('cms_moduls')->where('id_cms_moduls_group',$id_moduls_group)->count();
		$ro = intval($ro) + 1;
		echo $ro;
	}

	public function getArrSorting($id,$tipe) {
		$row = DB::table('cms_moduls')->where('id',$id)->first();

		if($tipe=='down') {
			$new_sorting = intval($row->sorting) + 1;
		}else{
			$new_sorting = intval($row->sorting) - 1;
		}		

		$new_sorting = ($new_sorting<=0)?1:$new_sorting;

		DB::table('cms_moduls')->where('id_cms_moduls_group',$row->id_cms_moduls_group)->where('sorting',$new_sorting)->update(array('sorting'=>$row->sorting));
		DB::table('cms_moduls')->where('id',$id)->update(array('sorting'=>$new_sorting));

		return redirect()->back()->with(['message'=>"Sort the data success !",'message_type'=>'success']);
	}
	
	
	
	public function postAddSave() {
		$this->validation();					
		$this->input_assignment();	

		//Generate Controller 
		$route_basename = basename(Request::get('path'));
		if($this->arr['controller']=='') $this->arr['controller'] = generate_controller(Request::get('table_name'),$route_basename);

		DB::table($this->table)->insert($this->arr);

		$id_modul = DB::getPdo()->lastInsertId();
		$user_id_privileges = Session::get('admin_privileges');
		DB::table('cms_privileges_roles')->insert(array(
				'id_cms_moduls'=>$id_modul,
				'id_cms_privileges'=>$user_id_privileges,
				'is_visible'=>1,
				'is_create'=>1,
				'is_read'=>1,
				'is_edit'=>1,
				'is_delete'=>1
			));


		$list = DB::table("cms_moduls")->where("id_cms_moduls_group",Request::input("id_cms_moduls_group"))->orderby("sorting",'asc')->get();
		$s = 1;
		foreach($list as $l) {
			DB::table("cms_moduls")->where("id",$l->id)->update(array("sorting"=>$s));
			$s++;
		}

		$ref_parameter = Request::input('ref_parameter');		
		if(Request::get('referal')) {
			return redirect(Request::get('referal').'?'.$ref_parameter)->with(['message'=>'The data has been added !','message_type'=>'success']);
		}else{
			if(Request::get('ref_mainpath')) {
				return redirect(Request::get('ref_mainpath'))->with(['message'=>"The data has been added !",'message_type'=>'success']);	
			}else{
				return redirect(mainpath())->with(['message'=>"The data has been added !",'message_type'=>'success']);
			}				
		}
		
	}
	
	
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();

		//Generate Controller 
		$route_basename = basename(Request::get('path'));
		if($this->arr['controller']=='') $this->arr['controller'] = generate_controller(Request::get('table_name'),$route_basename);

		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);


		$list = DB::table("cms_moduls")->where("id_cms_moduls_group",Request::input("id_cms_moduls_group"))->orderby("sorting",'asc')->get();
		$s = 1;
		foreach($list as $l) {
			DB::table("cms_moduls")->where("id",$l->id)->update(array("sorting"=>$s));
			$s++;
		}		
		return redirect()->back()->with(['message'=>"Update data success !",'message_type'=>'success']);
	}
	

	public function getApp() {
		$url = Request::get('u');
		$data['page_title'] = $this->data['priv']->name;
		$data['page_menu']  = Route::getCurrentRoute()->getActionName();
		$data['url'] = asset($url);
		return view('crudbooster::app',$data);
	}
	
}
