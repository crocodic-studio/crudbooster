<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Request;
use Session;
use Mail;
use Validator;
use DB;
use App;

class CustomApiController extends BaseController {
	
	/*
	USE THIS CONTROLLER FOR CUSTOM API WITHOUT API GENERATOR
	YOU CAN CUSTOM ALL API METHOD WITH YOUR WISHES
	USE $this->valid( array ) for validation
	USE $this->resp($status,$messaage,$data) for response
	YOUR API WOULD BE http://localhost/YourApp/apis/[method]
	*/

	/* START YOUR OWN METHODS AFTER THIS LINE */








	/* END YOU OWN METHODS */

	/* NO NEED MODIFY THESE BELLOW METHODS UNLESS YOU KNOW WHAT YOU ARE DOING */
	private function valid($arr=array(),$input_arr='') {
		$input_arr = (!$input_arr)?Request::all():$input_arr;

		foreach($arr as $a=>$b) {
			if(is_int($a)) {
				$arr[$b] = 'required';
			}else{
				$arr[$a] = $b;
			}
		}

		$validator = Validator::make($input_arr,$arr);
		
		if ($validator->fails()) 
		{
			$message = $validator->errors()->all();	
			$result = array();		
			$result['api_status'] = 0;
			$result['api_message'] = implode(', ',$message);
			$res = response()->json($result,400);
			$res->send();
			exit;
		}
	}

	private function resp($status,$message='',$data=array()) {
		if(!$status && !is_array($status)) {
			$r = array();
			$r['api_status'] = 0;
			$r['api_message'] = $message;			
			$res = response()->json($r,400);
			$res->send();
			exit;
		}

		if(is_array($status) || is_object($status)) {

			if (count($status) == 1) {
				$data['item'] = (array) $status;
			}else{
				$data['items'] = json_decode(json_encode($status),true);
			}

			$r = array();
			$r['api_status'] = true;
			$r['api_message'] = 'success';
			$r = array_merge($r,$data);
			$res = response()->json($r,200);
			$res->send();
			exit;
		}else{
			$newdata = array();			

			if(is_array($message) || is_object($message)) {
				$data = $message;
				if(is_object($data)) $data = json_decode(json_encode($data),true);
				$message = ($status)?"success":"failed";
			}
			
			if (count($data) == 1) {
				$newdata['item'] = (array) $data;
			}else{
				$newdata['items'] = json_decode(json_encode($data),true);
			}

			$r = array();
			$r['api_status'] = $status;
			$r['api_message'] = $message;
			if($data) $r = array_merge($r,$newdata);
			$res = response()->json($r,200);
			$res->send();
			exit;
		}		
	}

}




