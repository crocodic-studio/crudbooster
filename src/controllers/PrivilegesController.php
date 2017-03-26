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

class PrivilegesController extends CBController {

	
	public function cbInit() {
		$this->module_name = "Privilege";
		$this->table       = 'cms_privileges';
		$this->primary_key = 'id';
		$this->title_field = "name";	
		$this->button_import = false;
		$this->button_export = false;
		$this->button_action_style = 'button_icon';	
		$this->button_detail = false;

		$this->col   = array();
		$this->col[] = array("label"=>"ID","name"=>"id");
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Superadmin","name"=>"is_superadmin",'callback_php'=>'($row->is_superadmin)?"<span class=\"label label-success\">Superadmin</span>":"<span class=\"label label-default\">Standard</span>"');

		$this->form   = array();
		$this->form[] = array("label"=>"Name","name"=>"name",'required'=>true);
		$this->form[] = array("label"=>"Is Superadmin","name"=>"is_superadmin",'required'=>true);				
		$this->form[] = array("label"=>"Theme Color","name"=>"theme_color",'required'=>true);		
	}


	public function getAdd()
	{
		$this->cbLoader();

		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans('crudbooster.log_try_add',['module'=>CRUDBooster::getCurrentModule()->name ]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$id = 0; 
		$data['page_title'] = "Add Data";	
		$data['moduls'] = DB::table("cms_moduls")
		->where('is_protected',0)
		->select("cms_moduls.*",
			DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
			DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls  = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
			DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls    = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
			DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls    = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
			DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls  = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
			)
		->orderby("name","asc")->get();		
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('crudbooster::privileges',$data);
	}
	

	public function postAddSave() {
		$this->cbLoader();

		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation($request);				
		$this->input_assignment($request);		

		$this->arr[$this->primary_key] = DB::table($this->table)->max($this->primary_key) + 1;	

		DB::table($this->table)->insert($this->arr);
		$id = $this->arr[$this->primary_key];

		//set theme 
		Session::put('theme_color',$this->arr['theme_color']);

		$priv = Request::input("privileges");
		if($priv) {
			foreach($priv as $id_modul => $data) {
				$arrs = array();
				$arrs['id'] = DB::table('cms_privileges_roles')->max('id') + 1;
				$arrs['is_visible'] = @$data['is_visible']?:0;
				$arrs['is_create'] = @$data['is_create']?:0;
				$arrs['is_read'] = @$data['is_read']?:0;
				$arrs['is_edit'] = @$data['is_edit']?:0;
				$arrs['is_delete'] = @$data['is_delete']?:0;
				$arrs['id_cms_privileges'] = $id;
				$arrs['id_cms_moduls'] = $id_modul;			
				DB::table("cms_privileges_roles")->insert($arrs);


				$module = DB::table('cms_moduls')->where('id',$id_modul)->first();
				
				if($arrs['is_visible']==1) {		
				//Insert To Menu
				$menu = [];
				$menu['name'] = $module->name;
				$menu['type'] = 'Route';
				$menu['path'] = $module->controller.'GetIndex';
				$menu['color'] = 'normal';
				$menu['icon'] = $module->icon;
				$menu['parent_id'] = 0;
				$menu['is_active'] = 1;
				$menu['is_dashboard'] = 0;
				$menu['id_cms_privileges'] = $id;
				$menu['sorting'] = DB::table('cms_menus')->where('id_cms_privileges',$id)->max('sorting')+1;
				$menu['created_at'] = date('Y_m-d H:i:s');
				DB::table('cms_menus')->insert($menu);
				}
			}	
		}
		
		//Refresh Session Roles
		$roles = DB::table('cms_privileges_roles')
		->where('id_cms_privileges',CRUDBooster::myPrivilegeId())
		->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
		->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
		->get();
		Session::put('admin_privileges_roles',$roles);

		CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');		
	}
	
	public function getEdit($id)
	{
		$this->cbLoader();
		
		$row = DB::table($this->table)->where("id",$id)->first();		

		if(!CRUDBooster::isRead() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_edit",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$page_title = trans('crudbooster.edit_data_page_title',['module'=>'Privilege','name'=>$row->name]);

		$moduls = DB::table("cms_moduls")
		->where('is_protected',0)
		->select("cms_moduls.*")
		->orderby("name","asc")->get();
		$page_menu = Route::getCurrentRoute()->getActionName();
		return view('crudbooster::privileges',compact('row','page_title','moduls','page_menu'));
	}
	 
	public function postEditSave($id) {
		$this->cbLoader();

		$row = CRUDBooster::first($this->table,$id);

		if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$this->validation($request);
		$this->input_assignment($request,$id);

		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);
						
		$priv = Request::input("privileges");
		
		DB::table("cms_privileges_roles")->where("id_cms_privileges",$id)->delete();


		if($priv) {

			DB::table('cms_menus')->where('id_cms_privileges',$id)->delete();
						
			foreach($priv as $id_modul => $data) {
				$arrs = array();
				$arrs['id'] = DB::table('cms_privileges_roles')->max('id') + 1;
				$arrs['is_visible'] = @$data['is_visible']?:0;
				$arrs['is_create'] = @$data['is_create']?:0;
				$arrs['is_read'] = @$data['is_read']?:0;
				$arrs['is_edit'] = @$data['is_edit']?:0;
				$arrs['is_delete'] = @$data['is_delete']?:0;
				$arrs['id_cms_privileges'] = $id;
				$arrs['id_cms_moduls'] = $id_modul;			
				DB::table("cms_privileges_roles")->insert($arrs);

				$module = DB::table('cms_moduls')->where('id',$id_modul)->first();				
				
				if($arrs['is_visible']==1) {				
					//Insert To Menu
					$menu = [];
					$menu['name'] = $module->name;
					$menu['type'] = 'Route';
					$menu['path'] = $module->controller.'GetIndex';
					$menu['color'] = 'normal';
					$menu['icon'] = $module->icon;
					$menu['parent_id'] = 0;
					$menu['is_active'] = 1;
					$menu['is_dashboard'] = 0;
					$menu['id_cms_privileges'] = $id;
					$menu['sorting'] = DB::table('cms_menus')->where('id_cms_privileges',$id)->max('sorting')+1;
					$menu['created_at'] = date('Y_m-d H:i:s');
					DB::table('cms_menus')->insert($menu);
				}
				
			}
		}


		//Refresh Session Roles
		if($id==CRUDBooster::myPrivilegeId()) {
			$roles = DB::table('cms_privileges_roles')
			->where('id_cms_privileges',CRUDBooster::myPrivilegeId())
			->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
			->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
			->get();
			Session::put('admin_privileges_roles',$roles);

			Session::put('theme_color',$this->arr['theme_color']);
		}		

		CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_update_data_success",['module'=>"Privilege",'title'=>$row->name]),'success');
	}
	
	public function getDelete($id) {
		$this->cbLoader();
		
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isDelete() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

		DB::table($this->table)->where($this->primary_key,$id)->delete();
		DB::table("cms_privileges_roles")->where("id_cms_privileges",$row->id)->delete();
		DB::table('cms_menus')->where('id_cms_privileges',$id)->deleteE();
		CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_delete_data_success"),'success');		
	}

	
}
