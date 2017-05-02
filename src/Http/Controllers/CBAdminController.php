<?php

namespace Crocodicstudio\Crudbooster\Http\Controllers;

use Crocodicstudio\Crudbooster\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use CRUDBooster;

class CBAdminController extends CBController
{	

	function getIndex()
	{
		$dashboard = CB::sidebarDashboard();		
		if($dashboard && $dashboard->url) {
			return redirect($dashboard->url);
		}

		$data = array();			
		$data['page_title']       = '<strong>Dashboard</strong>';				
		return view('crudbooster::home',$data);
	}

	public function getLockScreen()
	{
		if(!CB::myId()) {
			Session::flush();
			return redirect()->route('getLogin')->with('message',trans('crudbooster.alert_session_expired'));
		}

		CB::setLocked();

		return view('crudbooster::lockscreen');
	}

	public function postUnlockScreen()
	{
		$id       = CRUDBooster::myId();
		$password = Request::input('password');		
		$users    = DB::table('cms_users')->where('id',$id)->first();		

		if(\Hash::check($password,$users->password)) {
			CB::unsetLocked();
			return redirect()->route('CBAdminControllerGetIndex'); 
		}else{
			echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";				
		}
	}	

	public function getLogin()
	{											
		return view('crudbooster::login');
	}
 
	public function postLogin()
	{		
		CB::valid([
			'email'=>'required|email|exists:cms_users',
			'password'=>'required'			
			],'view');

		$email 		= Request::input("email");
		$password 	= Request::input("password");
		$user 		= DB::table('cb_users')->where("email",$email)->first(); 		

		if(\Hash::check($password,$users->password)) {
			$rolesPK = CB::findPrimaryKey('cb_roles');
			$role = DB::table("cb_roles")->where($rolesPK,$user->cb_roles_id)->first();

			$permissions = DB::table('cb_permissions')
			->where('cb_roles_id',$user->cb_roles_id)
			->join('cb_modules','cb_modules.id','=','id_cb_modules')
			->select('cb_modules.name','cb_modules.path','can_visible','can_create','can_read','can_edit','can_delete')
			->get();

			$photo = ($user->photo)?asset($user->photo):'https://www.gravatar.com/avatar/'.md5($user->email).'?s=100';

			Session::put('cb_id',$user->id);			
			Session::put('cb_is_superadmin',$role->is_superadmin);
			Session::put('cb_name',$users->name);	
			Session::put('cb_photo',$photo);
			Session::put('cb_roles',$roles);
			Session::put("cb_roles_id",$user->cb_roles_id);
			Session::put('cb_roles_name',$role->name);			
			Session::put('cb_is_locked',0);
			Session::put('theme_color',$role->theme_color);			

			CRUDBooster::insertLog(trans("crudbooster.log_login",['email'=>$user->email,'ip'=>Request::server('REMOTE_ADDR')]));		

			$cbHook = new \App\Http\Controllers\CBHook;
			$cbHook->afterLogin();

			return redirect()->route('CBAdminControllerGetIndex'); 
		}else{
			return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_password_wrong'));			
		}
	}

	public function getForgot()
	{		
		return view('crudbooster::forgot');
	}

	public function postForgot()
	{
		CB::valid([
			'email'=>'required|email|exists:cms_users'			
			],'view');	

		$password = str_random(5);
		$passwordHash = \Hash::make($password);

		DB::table('cms_users')->where('email',Request::input('email'))->update(array('password'=>$passwordHash));

		$appname = CB::getSetting('appname');		
		$user = CB::first('cms_users',['email'=>g('email')]);	
		$user->password = $password;
		CB::sendEmail(['to'=>$user->email,'data'=>$user,'template'=>'forgot_password_backend']);

		CRUDBooster::insertLog(trans("crudbooster.log_forgot",['email'=>g('email'),'ip'=>Request::server('REMOTE_ADDR')]));

		return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));
	}	

	public function getLogout()
	{
		$me = CRUDBooster::me();
		CRUDBooster::insertLog(trans("crudbooster.log_logout",['email'=>$me->email]));

		Session::flush();
		return redirect()->route('getLogin')->with('message',trans("crudbooster.message_after_logout"));
	}

}
