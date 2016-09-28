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

class PrivilegesController extends CBController {

	
	public function __construct() {
		$this->module_name = "Privilege";
		$this->table       = 'cms_privileges';
		$this->primary_key = 'id';
		$this->title_field = "name";		

		$this->col   = array();
		$this->col[] = array("label"=>"ID","name"=>"id");
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Superadmin","name"=>"is_superadmin",'callback_php'=>'($row->is_superadmin)?"<span class=\"label label-success\">Superadmin</span>":"<span class=\"label label-default\">Standard</span>"');
		$this->col[] = array("label"=>"Avail. Register","name"=>"is_register",'callback_php'=>'($row->is_register)?"<span class=\"label label-success\">Available</span>":"<span class=\"label label-default\">Not Available</span>"');

		$this->form   = array();
		$this->form[] = array("label"=>"Name","name"=>"name",'required'=>true);
		$this->form[] = array("label"=>"Is Superadmin","name"=>"is_superadmin",'required'=>true);		
		$this->form[] = array("label"=>"Is Register","name"=>"is_register",'required'=>true);		
		$this->form[] = array("label"=>"Theme Color","name"=>"theme_color",'required'=>true);

		$this->addaction[] = array('label'=>'Configuration Dashboard',
			'route'=>url("admin/set-dashboard-config-mode?id_cms_privileges=[id]"),
			'icon'=>'fa fa-tachometer');

		$this->constructor();
	}


	public function getAdd()
	{
		$id = 0; 
		$data['page_title'] = "Add Data";	
		$data['moduls'] = DB::table("cms_moduls")
		->select("cms_moduls.*",
			DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
			DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls  = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
			DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls    = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
			DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls    = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
			DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls  = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
			)
		->orderby("name","asc")->get();		
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('crudbooster::default.privileges',$data);
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
			DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
			DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
			DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
			DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
			DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
			)
		->orderby("name","asc")->get();
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('crudbooster::default.privileges',$data);
	}
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();
		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);

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
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();
		DB::table($this->table)->where($this->primary_key,$id)->delete();
		DB::table("cms_privileges_roles")->where("id_cms_privileges",$row->id)->delete();
		return redirect()->back()->with(['message'=>"Data has been deleted !",'message_type'=>"success"]);
	}

	
}
