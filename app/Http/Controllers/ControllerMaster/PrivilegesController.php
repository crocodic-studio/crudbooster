<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class PrivilegesController extends Controller {

	
	public function __construct() {
		$this->modulname = "Privilege";
		$this->table = 'cms_privileges';
		$this->primkey = 'id';
		$this->titlefield = "name";

		$this->theme = 'admin.default';	
		$this->prefixroute = 'admin/';		

		$this->col = array();
		$this->col[] = array("label"=>"ID","field"=>"id");
		$this->col[] = array("label"=>"Name","field"=>"name");

		$this->form = array();
		$this->form[] = array("label"=>"Name","name"=>"name");
		$this->form[] = array("label"=>"Is Superadmin","name"=>"is_superadmin");
		//$this->form[] = array("label"=>"Filter Field","name"=>"filter_field");
		$this->form[] = array("label"=>"Theme Color","name"=>"theme_color");

		$this->addaction[] = array('label'=>'Configuration Dashboard','route'=>url("admin/set-dashboard-config-mode?id_cms_privileges=%id%"),'icon'=>'fa fa-tachometer');

		$this->constructor();
	}


	public function getAdd()
	{
		$data['page_title'] = "Add Data";	
		$data['moduls'] = DB::table("cms_moduls")
		->select("cms_moduls.*",
			DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_visible"),
			DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_create"),
			DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_read"),
			DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_edit"),
			DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_delete")
			)
		->orderby("name","asc")->get();		
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('admin.default.privileges',$data);
	}
	

	public function postAddSave() {
		$this->validation();				
		$this->input_assignment();		
		DB::table($this->table)->insert($this->arr);
		$id = DB::getPdo()->lastInsertId();

		//set theme 
		Session::put('theme_color',$this->arr['theme_color']);

		$priv = Request::input("privileges");
		if($priv) {
			foreach($priv as $id_modul => $data) {
				$arrs = array();
				$arrs['is_visible'] = @$data['is_visible']?:0;
				$arrs['is_create'] = @$data['is_create']?:0;
				$arrs['is_read'] = @$data['is_read']?:0;
				$arrs['is_edit'] = @$data['is_edit']?:0;
				$arrs['is_delete'] = @$data['is_delete']?:0;
				$arrs['id_cms_privileges'] = $id;
				$arrs['id_cms_moduls'] = $id_modul;			
				DB::table("cms_privileges_roles")->insert($arrs);
			}	
		}
				
		return redirect($this->dashboard)->with(['message'=>"Data has been added !",'message_type'=>'success']);
	}
	
	public function getEdit($id)
	{
		$data['page_title'] = "Edit Data";	
		$data['row'] = DB::table($this->table)->where("id",$id)->first();

		$data['moduls'] = DB::table("cms_moduls")
		->select("cms_moduls.*",
			DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_visible"),
			DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_create"),
			DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_read"),
			DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_edit"),
			DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id' limit 1) as is_delete")
			)
		->orderby("name","asc")->get();
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('admin.default.privileges',$data);
	}
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();
		DB::table($this->table)->where($this->primkey,$id)->update($this->arr);

		//set theme 
		Session::put('theme_color',$this->arr['theme_color']);

		
		$priv = Request::input("privileges");
		if($priv) {
			DB::table("cms_privileges_roles")->where("id_cms_privileges",$id)->delete();
			foreach($priv as $id_modul => $data) {
				$arrs = array();
				$arrs['is_visible'] = @$data['is_visible']?:0;
				$arrs['is_create'] = @$data['is_create']?:0;
				$arrs['is_read'] = @$data['is_read']?:0;
				$arrs['is_edit'] = @$data['is_edit']?:0;
				$arrs['is_delete'] = @$data['is_delete']?:0;
				$arrs['id_cms_privileges'] = $id;
				$arrs['id_cms_moduls'] = $id_modul;			
				DB::table("cms_privileges_roles")->insert($arrs);
			}
		}
		return redirect($this->dashboard)->with(['message'=>"Data has been updated !",'message_type'=>'success']);
	}
	
	public function getDelete($id) {
		$row = DB::table($this->table)->where($this->primkey,$id)->first();
		DB::table($this->table)->where($this->primkey,$id)->delete();
		DB::table("cms_privileges_roles")->where("id_cms_privileges",$row->id)->delete();
		return redirect()->back()->with(['message'=>"Data has been deleted !",'message_type'=>"success"]);
	}

	
}
