<?php

namespace Crocodicstudio\Crudbooster\Http\Controllers;

use Crocodicstudio\Crudbooster\Http\Controllers\Controller;
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

class CBRolesController extends CBController
{
	public function cbInit()
	{		
		$this->table             = 'cb_roles';
		$this->primaryKey        = 'id';
		$this->titleField        = 'name';
		$this->buttonImport      = false;
		$this->buttonExport      = false;
		$this->buttonActionStyle = 'button_icon';
		$this->buttonDetail      = false;
		$this->buttonBulkAction  = false;

		$this->columns   = [];
		$this->columns[] = ["label"=>"ID","name"=>"id"];
		$this->columns[] = ["label"=>"Name","name"=>"name"];
		$this->columns[] = ["label"=>"Superadmin","name"=>"is_superadmin",'callback'=>function($row) {
			if($row->is_superadmin) 
				return "<span class='label label-success'>Superadmin</span>";
			else 
				return "<span class='label label-default'>Regular User</span>";
		});

		$this->inputs   = [];
		$this->inputs[] = ["label"=>"Name","name"=>"name",'required'=>true];
		$this->inputs[] = ["label"=>"Is Superadmin","name"=>"is_superadmin",'required'=>true];
		$this->inputs[] = ["label"=>"Theme Color","name"=>"theme_color",'required'=>true];
	}

	public function getAdd()
	{		

		if (!CB::canCreate() && $this->globalRoles==FALSE) {			
			CB::insertLog(trans('crudbooster.log_try_add',['module'=>CB::getCurrentModule()->name ]));
			CB::redirect(CB::adminPath(),trans("crudbooster.denied_access"));
		}

		$id = 0;
		$data['page_title'] = lang('crudbooster.button_add');
		$data['modules'] = DB::table("cb_modules")
		->where('is_protected',0)
		->select("cb_modules.*",
			DB::raw("(select can_visible from cb_permissions where cb_modules_id = cb_modules.id and cb_roles_id = '$id') as can_visible"),
			DB::raw("(select can_create from cb_permissions where cb_modules_id  = cb_modules.id and cb_roles_id = '$id') as can_create"),
			DB::raw("(select is_read from cb_permissions where cb_modules_id    = cb_modules.id and cb_roles_id = '$id') as is_read"),
			DB::raw("(select is_edit from cb_permissions where cb_modules_id    = cb_modules.id and cb_roles_id = '$id') as is_edit"),
			DB::raw("(select is_delete from cb_permissions where cb_modules_id  = cb_modules.id and cb_roles_id = '$id') as is_delete")
			)
		->orderby("name","asc")->get();
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('CB::privileges',$data);
	}

	public function postAddSave()
	{		

		if (!CB::canCreate() && $this->globalRoles==FALSE) {
			CB::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->titleField),'module'=>CB::getCurrentModule()->name ]));
			CB::redirect(CB::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation();
		$this->inputAssigment();

		$this->arr[$this->primaryKey] = CB::newId($this->table);
		DB::table($this->table)->insert($this->arr);
		$id = $this->arr[$this->primaryKey];

		//set theme
		Session::put('theme_color',$this->arr['theme_color']);		

		if (Request::input("permissions")) {
			foreach(Request::input("permissions") as $modules_id => $permission) {
				$data                  = [];
				$data['id']            = CB::newId('cb_permissions');
				$data['can_visible']   = @$data['can_visible']?:0;
				$data['can_create']    = @$data['can_create']?:0;
				$data['can_read']      = @$data['can_read']?:0;
				$data['can_edit']      = @$data['can_edit']?:0;
				$data['can_delete']    = @$data['can_delete']?:0;
				$data['cb_roles_id']   = $id;
				$data['cb_modules_id'] = $id_modul;
				DB::table("cb_permissions")->insert($data);

				$module = DB::table('cb_modules')->where('id',$modules_id)->first();

				if ($data['can_visible']==1) {
					//Insert To Menu
					$dataMenu                 = [];
					$dataMenu['name']         = $module->name;
					$dataMenu['type']         = 'Route';
					$dataMenu['path']         = $module->controller.'GetIndex';
					$dataMenu['color']        = 'normal';
					$dataMenu['icon']         = $module->icon;
					$dataMenu['parent_id']    = 0;
					$dataMenu['is_active']    = 1;
					$dataMenu['is_dashboard'] = 0;
					$dataMenu['cb_roles_id']  = $id;
					$dataMenu['sorting']      = DB::table('cb_menus')->where('cb_roles_id',$id)->max('sorting')+1;
					$dataMenu['created_at']   = date('Y_m-d H:i:s');
					DB::table('cb_menus')->insert($dataMenu);
				}
			}
		}

		//Refresh Session Roles
		$permissions = DB::table('cb_permissions')
		->where('cb_roles_id',CB::myPrivilegeId())
		->join('cb_modules','cb_modules.id','=','cb_modules_id')
		->select('cb_modules.name','cb_modules.path','can_visible','can_create','is_read','is_edit','is_delete')
		->get();

		Session::put('cb_permissions',$permissions);

		CB::redirect(CB::mainpath(),trans("crudbooster.alert_add_data_success"),'success');		
	}
	
	public function getEdit($id)
	{		
		$row = CB::first($this->table,$id);

		if (!CB::canEdit() && $this->globalRoles==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_edit",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$pageTitle = trans('crudbooster.edit_data_page_title',['module'=>'Role','name'=>$row->name]);
		$modules = DB::table("cb_modules")->where('is_protected',0)
											->select("cb_modules.*")
											->orderby("name","asc")->get();		
		return view('CB::privileges',compact('row','page_title','modules'));
	}

	public function postEditSave($id)
	{		
		$row = CB::first($this->table,$id);

		if (!CB::canEdit() && $this->globalRoles==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$this->validation();
		$this->inputAssigment($id);

		DB::table($this->table)->where($this->primaryKey,$id)->update($this->inputAssigmentData);

		$permissions = Request::input("permissions");

		DB::table("cb_permissions")->where("cb_roles_id",$id)->delete();

		if ($permissions) {

			DB::table('cb_menus')->where('cb_roles_id',$id)->delete();

			foreach ($permissions as $modules_id => $permission) {
				$dataPermissions = [];
				$dataPermissions['id'] = CB::newId('cb_permissions');
				$dataPermissions['can_visible'] = @$data['can_visible']?:0;
				$dataPermissions['can_create'] = @$data['can_create']?:0;
				$dataPermissions['can_read'] = @$data['can_read']?:0;
				$dataPermissions['can_edit'] = @$data['can_edit']?:0;
				$dataPermissions['can_delete'] = @$data['can_delete']?:0;
				$dataPermissions['cb_roles_id'] = $id;
				$dataPermissions['cb_modules_id'] = $id_modul;
				DB::table("cb_permissions")->insert($dataPermissions);

				$module = CB::first('cb_modules',$modules_id);

				if ($dataPermissions['can_visible']==1) {
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
					$menu['cb_roles_id'] = $id;
					$menu['sorting'] = DB::table('cb_menus')->where('cb_roles_id',$id)->max('sorting')+1;
					$menu['created_at'] = date('Y_m-d H:i:s');
					DB::table('cb_menus')->insert($menu);
				}
			}
		}

		//Refresh Session Roles
		if ($id==CB::myRoleId()) {
			$permissions = DB::table('cb_permissions')
			->where('cb_roles_id',CB::myRoleId())
			->join('cb_modules','cb_modules.id','=','cb_modules_id')
			->select('cb_modules.name','cb_modules.path','can_visible','can_create','can_read','can_edit','can_delete')
			->get();
			Session::put('cb_permissions',$permissions);

			Session::put('theme_color',$this->arr['theme_color']);
		}		

		CB::redirect(CB::mainpath(),trans("crudbooster.alert_update_data_success",['module'=>"Role",'title'=>$row->name]),'success');
	}

	public function getDelete($id)
	{				
		$row = CB::first($this->table,$id);

		if (!CB::canDelete() && $this->globalRoles==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		DB::table($this->table)->where($this->primaryKey,$id)->delete();
		DB::table("cb_permissions")->where("cb_roles_id",$row->id)->delete();
		DB::table('cb_menus')->where('cb_roles_id',$id)->delete();
		CB::redirect(CB::mainpath(),trans("crudbooster.alert_delete_data_success"),'success');
	}

}
