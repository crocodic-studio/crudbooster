<?php namespace App\Http\Controllers;

date_default_timezone_set("Asia/Jakarta");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

use Illuminate\Routing\Controller as BaseController;

use Request;
use Session;
use Mail;
use Validator;
use DB;
use App;

class FrontController extends BaseController {
	

	function getIndex() {
		$data['title_meta'] = "Welcome Page - Its Work";
		return view('home',$data);
	}

	

}




