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
		$this->modulname = "Modul";
		$this->table = 'cms_moduls';
		$this->primkey = 'id';
		$this->titlefield = "name";
		$this->limit = 100;

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';

		
		$this->col = array();
		$this->col[] = array('label'=>'Sorting','field'=>'sorting');
		$this->col[] = array("label"=>"Nama","field"=>"name");
		$this->col[] = array("label"=>"Path","field"=>"path");
		$this->col[] = array("label"=>"Controller","field"=>"controller");
		$this->col[] = array("label"=>"Group","field"=>"nama_group","join"=>"cms_moduls_group");		
		$this->col[] = array("label"=>"Active","field"=>"is_active","callback_php"=>"(%field%)?'<span class=\"label label-success\">Active</span>':'<span class=\"label label-default\">Not Active</span>'");		

		$this->form = array();		
		$this->form[] = array("label"=>"Nama","name"=>"name");
		$this->form[] = array("label"=>"Path","name"=>"path","value"=>"admin/");

		$datacontroller = array();
		$filesphp = glob("app/Http/Controllers/*.php");
		$master = glob("app/Http/Controllers/ControllerMaster/*.php");
		$filesphp = array_merge($filesphp,$master);
		foreach ($filesphp as $filename) {
			$namafile = str_replace(".php","",basename($filename));
			$datacontroller[] = $namafile;
		}

		$this->form[] = array("label"=>"Controller","name"=>"controller","type"=>"select","dataenum"=>$datacontroller);
		$this->form[] = array("label"=>"SQL Where Query","name"=>"sql_where","type"=>"text","help"=>"You can use alias [admin_id],[admin_id_companies]");

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
		$this->addaction[] = array('label'=>'Edit PHP Controller','route'=>url($this->mainpath('edit-php/%id%')),'icon'=>'fa fa-cog','color'=>'primary');
				
		$this->addaction[] = array('label'=>'Up','route'=>url($this->mainpath('arr-sorting/%id%/up')),'icon'=>'fa fa-arrow-up','ajax'=>true);
		$this->addaction[] = array('label'=>'Down','route'=>url($this->mainpath('arr-sorting/%id%/down')),'icon'=>'fa fa-arrow-down','ajax'=>true);
		
		$this->index_orderby = array("id_cms_moduls_group"=>"asc","sorting"=>"asc");

		$this->constructor();
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

		return redirect()->back()->with(['message'=>"Berhasil sorting data !",'message_type'=>'success']);
	}
	
	
	
	public function postAddSave() {
		$this->validation();				
		$this->input_assignment();	
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

		return redirect($this->dashboard)->with(['message'=>"Berhasil tambah data !",'message_type'=>'success']);
	}
	
	
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();
		DB::table($this->table)->where($this->primkey,$id)->update($this->arr);


		$list = DB::table("cms_moduls")->where("id_cms_moduls_group",Request::input("id_cms_moduls_group"))->orderby("sorting",'asc')->get();
		$s = 1;
		foreach($list as $l) {
			DB::table("cms_moduls")->where("id",$l->id)->update(array("sorting"=>$s));
			$s++;
		}		
		return redirect($this->dashboard)->with(['message'=>"Berhasil update data !",'message_type'=>'success']);
	}
	

	public function getApp() {
		$url = Request::get('u');
		$data['page_title'] = $this->data['priv']->name;
		$data['page_menu']  = Route::getCurrentRoute()->getActionName();
		$data['url'] = asset($url);
		return view('admin.app',$data);
	}
	
}
