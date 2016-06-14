<?php namespace App\Http\Controllers;

use Request;
use Session;
use DB;

class AdminSettingsController extends Controller {	
 
	public function getMeta() {
		$data['page_title'] = "Setting";		

		$rows = array();
		$row = DB::table("cms_settings")->get();
		foreach($row as $r) {
			$rows[$r->name] = $r->value;
		}
		$data['row'] = json_decode(json_encode($rows));

		return view("admin.settings_meta",$data);
	}

	public function postSave() {
		$post = Request::all();
		if($post) {			
			foreach($post as $a=>$b) {
				if($a=='_token') continue;

				DB::table("cms_settings")->where("name",$a)->delete();

				$arr = array();
				$arr["name"] = $a;
				$arr['value'] = $b;
				DB::table("cms_settings")->insert($arr);

			}
			return redirect()->back()->with(['message'=>"Berhasil simpan data !",'message_type'=>'success']);
		}else{
			return redirect()->back()->with(['message'=>"Gagal simpan data !",'message_type'=>'warning']);
		}
	}
}
