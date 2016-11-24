<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use CRUDBooster;

class AdminController extends CBController {	

	function getIndex(Request $request) {

		$dashboard = CRUDBooster::sidebarDashboard();
		if($dashboard && $dashboard->url) {
			return redirect($dashboard->url.'?m='.$request->get('m').'&d='.$request->get('d'));
		}

		$data = array();			
		$data['page_title']       = '<strong>Dashboard</strong>';		
		$data['page_menu']        = Route::getCurrentRoute()->getActionName();		
		return view('crudbooster::home',$data);
	}

	public function getLockscreen(Request $request) {
		
		if(!CRUDBooster::myId()) {
			$request->session()->flush();
			return redirect()->route('getLogin')->with('message',trans('crudbooster.alert_session_expired'));
		}
		
		$request->session()->put('admin_lock',1);
		return view('crudbooster::lockscreen');
	}

	public function postUnlockScreen(Request $request) {
		$id       = CRUDBooster::myId();
		$password = $request->input('password');		
		$users    = DB::table(config('crudbooster.USER_TABLE'))->where('id',$id)->first();		

		if(\Hash::check($password,$users->password)) {
			$request->session()->put('admin_lock',0);	
			return redirect()->route('AdminControllerGetIndex'); 
		}else{
			echo "<script>alert('".trans('crudbooster.alert_password_wrong')."');history.go(-1);</script>";				
		}
	}

	public function getLogin()
	{											
		return view('crudbooster::login');
	}
 
	public function postLogin(Request $request) {		

		$validator = Validator::make($request->all(),			
			[
			'email'=>'required|email|exists:'.config('crudbooster.USER_TABLE'),
			'password'=>'required'			
			]
		);
		
		if ($validator->fails()) 
		{
			$message = $validator->errors()->all();
			return redirect()->back()->with(['message'=>implode(', ',$message),'message_type'=>'danger']);
		}

		$email 		= $request->input("email");
		$password 	= $request->input("password");
		$users 		= DB::table(config('crudbooster.USER_TABLE'))->where("email",$email)->first(); 		

		if(\Hash::check($password,$users->password)) {
			$priv = DB::table("cms_privileges")->where("id",$users->id_cms_privileges)->first();

			$roles = DB::table('cms_privileges_roles')
			->where('id_cms_privileges',$users->id_cms_privileges)
			->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
			->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
			->get();
			
			$photo = ($users->photo)?asset($users->photo):'https://www.gravatar.com/avatar/'.md5($users->email).'?s=100';
			$request->session()->put('admin_id',$users->id);			
			$request->session()->put('admin_is_superadmin',$priv->is_superadmin);
			$request->session()->put('admin_name',$users->name);	
			$request->session()->put('admin_photo',$photo);
			$request->session()->put('admin_privileges_roles',$roles);
			$request->session()->put("admin_privileges",$users->id_cms_privileges);
			$request->session()->put('admin_privileges_name',$priv->name);			
			$request->session()->put('admin_lock',0);
			$request->session()->put('theme_color',$priv->theme_color);
			$request->session()->put("appname",CRUDBooster::getSetting('appname'));		

			CRUDBooster::insertLog(trans("crudbooster.log_login",['email'=>$users->email,'ip'=>$request->server('REMOTE_ADDR')]));		

			$cb_hook_session = new \App\Http\Controllers\CBHook;
			$cb_hook_session->afterLogin();

			return redirect()->route('AdminControllerGetIndex'); 
		}else{
			return redirect()->route('getLogin')->with('message', trans('crudbooster.alert_password_wrong'));			
		}		
	}

	public function getForgot() {		
		return view('crudbooster::forgot');
	}

	public function postForgot(Request $request) {
		$validator = Validator::make($request->all(),			
			[
			'email'=>'required|email|exists:'.config('crudbooster.USER_TABLE')			
			]
		);
		
		if ($validator->fails()) 
		{
			$message = $validator->errors()->all();
			return redirect()->back()->with(['message'=>implode(', ',$message),'message_type'=>'danger']);
		}	

		$rand_string = str_random(5);
		$password = \Hash::make($rand_string);

		DB::table(config('crudbooster.USER_TABLE'))->where('email',$request->input('email'))->update(array('password'=>$password));
 	
		$appname = CRUDBooster::getSetting('appname');		
		$user = CRUDBooster::first(config('crudbooster.USER_TABLE'),['email'=>g('email')]);	
		$user->password = $rand_string;
		CRUDBooster::sendEmail(['to'=>$to,'data'=>$user,'template'=>'forgot_password_backend']);

		CRUDBooster::insertLog(trans("crudbooster.log_forgot",['email'=>g('email'),'ip'=>$request->server('REMOTE_ADDR')]));

		return redirect()->route('getLogin')->with('message', trans("crudbooster.message_forgot_password"));

	}	

	public function getLogout(Request $request) {
		$request->session()->flush();
		$me = CRUDBooster::me();
		CRUDBooster::insertLog(trans("crudbooster.log_logout",['email'=>$me->email]));

		return redirect()->route('getLogin')->with('message',trans("crudbooster.message_after_logout"));
	}

}
