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

class ModulsController extends Controller {
	
	public function __construct() {		
		$this->table = 'cms_moduls';
		$this->primkey = 'id';
		$this->titlefield = "name";
		$this->limit = 100;

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';

		
		$this->col = array();
		$this->col[] = array('label'=>'Sorting','field'=>'sorting');
		$this->col[] = array("label"=>"Name","field"=>"name");
		$this->col[] = array("label"=>"Path","field"=>"path"); 
		$this->col[] = array("label"=>"Table","field"=>"table_name");
		$this->col[] = array("label"=>"Controller","field"=>"controller");
		$this->col[] = array("label"=>"Group","field"=>"id_cms_moduls_group","join"=>"cms_moduls_group,nama_group");		
		$this->col[] = array("label"=>"Active","field"=>"is_active","callback_php"=>"(%field%)?'<span class=\"label label-success\">Active</span>':'<span class=\"label label-default\">Not Active</span>'");		

		$this->form = array();		
		$this->form[] = array("label"=>"Name","name"=>"name");

		$exception_table = array('cms_dashboard','cms_logs','cms_moduls','cms_moduls_group','cms_privileges','cms_privileges_roles','cms_users','cms_apicustom','cms_settings','cms_companies','cms_filemanager');
		$tables = DB::select('SHOW TABLES');
		$tables_list = array();		
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = $value;	
				if(is_exists_controller($value)) {
					$label = $label." [Already]";
				}
				$tables_list[] = $value."|".$label;
			}
		}



		$this->form[] = array("label"=>"Table Name","name"=>"table_name","type"=>"select","dataenum"=>$tables_list);		

		$this->form[] = array("label"=>"Route","name"=>"path","value"=>"admin/","jquery"=>"
			$('#table_name').change(function() {
				var v = $(this).val();
				$('#path').val('admin/'+v);
			})
			");

		$this->form[] = array("label"=>"Controller","name"=>"controller","type"=>"text","placeholder"=>"Auto Generated");
		
		$this->form[] = array("label"=>"SQL Where Query","name"=>"sql_where","type"=>"text","placeholder"=>"Example : columnname = value AND columnname2 = value2","help"=>"You can use alias [admin_id],[admin_id_companies]");

		$this->form[] = array("label"=>"SQL Order By","name"=>"sql_orderby","type"=>"text","placeholder"=>"Enter query here","help"=>"Example : column_name ASC, column2_name DESC");
		$this->form[] = ['label'=>"Limit Data","name"=>"limit_data","type"=>"text","placeholder"=>"Example : 10"];

		$this->form[] = array("label"=>"Icon","name"=>"icon","type"=>"radio","dataenum"=>array(
				"fa fa-cog|<i class='fa fa-cog'></i>",
				"fa fa-comment|<i class='fa fa-comment'></i>",
				"fa fa-users|<i class='fa fa-users'></i>",
				"fa fa-file|<i class='fa fa-file'></i>",
				"fa fa-database|<i class='fa fa-database'></i>",
				"fa fa-bank|<i class='fa fa-bank'></i>",
				"fa fa-bars|<i class='fa fa-bars'></i>",
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
				"fa fa-hourglass|<i class='fa fa-hourglass'></i>",
				"fa fa-dashboard|<i class='fa fa-dashboard'></i>",
				"fa fa-flag-o|<i class='fa fa-flag-o'></i>",
				"fa fa-folder-open|<i class='fa fa-folder-open'></i>",
				"fa fa-credit-card|<i class='fa fa-credit-card'></i>",
				"fa fa-inbox|<i class='fa fa-inbox'></i>",
				"fa fa-spinner|<i class='fa fa-spinner'></i>",
				"fa fa-line-cart|<i class='fa fa-line-cart'></i>",
				"fa fa-info-circle|<i class='fa fa-info-circle'></i>",
				"fa fa-cloud-download|<i class='fa fa-cloud-download'></i>",
			),"value"=>"fa fa-bars");
		
		$this->form[] = array("label"=>"Group","name"=>"id_cms_moduls_group","required"=>"required","type"=>"select","datatable"=>"cms_moduls_group,nama_group");		
		$this->form[] = array("label"=>"Active ?","name"=>"is_active","type"=>"select","dataenum"=>array("1|Aktif","0|Tidak Aktif"),"value"=>1);
		
		$url_find_sorting = action('ModulsController@getFindLastSorting');
		$this->form[] = array("label"=>"Sorting","name"=>"sorting","jquery"=>"
				$('#id_cms_moduls_group').change(function() {
					var id = $(this).val();
					$.get('$url_find_sorting/'+id,function(resp) {
						$('#sorting').val(resp);
					});
				})
			");				
		

		$this->addaction = array();
		$this->addaction[] = array('label'=>'Edit PHP Controller','route'=>action('ModulsController@getEditPhp').'/%id%','icon'=>'fa fa-cog','color'=>'primary');				
		$this->addaction[] = array('label'=>'Up','route'=>action('ModulsController@getArrSorting').'/%id%/up','icon'=>'fa fa-arrow-up','ajax'=>true);
		$this->addaction[] = array('label'=>'Down','route'=>action('ModulsController@getArrSorting').'/%id%/down','icon'=>'fa fa-arrow-down','ajax'=>true);
		
		$this->index_orderby = array("id_cms_moduls_group"=>"asc","sorting"=>"asc");

		$this->constructor();
	}

	function hook_after_delete($id) {
		$modul = DB::table('cms_moduls')->where('id',$id)->first();
		@unlink('app/Http/Controllers/'.$modul->controller.'.php');		
	}

	public function getEditPhp($id_cms_moduls) {
		$moduls = DB::table('cms_moduls')->where('id',$id_cms_moduls)->first();

		$data['page_title'] = "Edit PHP Controller : $moduls->controller";
		$data['page_menu']  = Route::getCurrentRoute()->getActionName();

		if(file_exists("app/Http/Controllers/".$moduls->controller.".php")) {
			$data['content_php'] = file_get_contents("app/Http/Controllers/".$moduls->controller.".php");	
		}elseif (file_exists("app/Http/Controllers/ControllerMaster/".$moduls->controller.".php")) {
			$data['content_php'] = file_get_contents("app/Http/Controllers/ControllerMaster/".$moduls->controller.".php");
		}else{
			$data['content_php'] = '';
		}
		
		return view('admin.controllers_edit',$data);
	}

	public function postEditPhp($id_cms_moduls) {
		$content = Request::input("content_php");
		$moduls = DB::table('cms_moduls')->where('id',$id_cms_moduls)->first();

		if(file_exists("app/Http/Controllers/".$moduls->controller.".php")) {
			if(file_put_contents("app/Http/Controllers/".$moduls->controller.".php",$content)) echo 1;
			else echo 0;
		}elseif (file_exists("app/Http/Controllers/ControllerMaster/".$moduls->controller.".php")) {
			if(file_put_contents("app/Http/Controllers/ControllerMaster/".$moduls->controller.".php",$content)) echo 1;
			else echo 0;
		}else{
			if(file_put_contents("app/Http/Controllers/".$moduls->controller.".php",$content)) echo 1;
			else echo 0;
		}
	}

	public function getFindLastSorting($id_moduls_group) {
		$ro = DB::table('cms_moduls')->where('id_cms_moduls_group',$id_moduls_group)->count();
		$ro = $ro + 1;
		echo $ro;
	}

	public function getArrSorting($id,$tipe) {
		$row = DB::table('cms_moduls')->where('id',$id)->first();

		if($tipe=='down') {
			$new_sorting = $row->sorting + 1;
		}else{
			$new_sorting = $row->sorting - 1;
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
			return redirect(Request::get('referal'))->with(['message'=>'Add new data success !','message_type'=>'success']);
		}else{
			return redirect($this->mainpath().'/edit/'.$lastid.'?'.$ref_parameter)->with(['message'=>"Add new data success !",'message_type'=>'success']);	
		}
		
	}
	
	
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();

		//Generate Controller 
		$route_basename = basename(Request::get('path'));
		if($this->arr['controller']=='') $this->arr['controller'] = generate_controller(Request::get('table_name'),$route_basename);

		DB::table($this->table)->where($this->primkey,$id)->update($this->arr);


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
		return view('admin.app',$data);
	}
	
}
