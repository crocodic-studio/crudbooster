<?php namespace App\Http\Controllers;

date_default_timezone_set("Asia/Jakarta");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Request;
use Session;
use Mail;
use Validator;
use DB;
use App;
use PDF;
use Excel;
use Hash;
use Cache;

abstract class ApiController extends BaseController {
	
	use DispatchesCommands, ValidatesRequests;

	var $setting;
	var $permalink;
	var $hook_api_status;
	var $hook_api_message;	
	var $last_id_tmp       = array();
	

	public function hook_before(&$postdata) {

	}
	public function hook_after($postdata,&$result) {
		
	}

	public function hook_query_list(&$data) {

	}

	public function hook_query_detail(&$data) {

	}

	public function hook_api_status($boolean) {
		$this->hook_api_status = $boolean;
	}
	public function hook_api_message($message) {
		$this->hook_api_message = $message;
	}

	public function apiValidator($posts,$rules) {
		$validator = Validator::make($posts,$rules);
		
		if ($validator->fails()) 
		{
			$message = $validator->errors()->all();
		
			$result['api_status'] = 0;
			$result['api_message'] = implode(',', $message);
			response()->json($result)->send();
			exit;
		}
	}

	public function execute_api() {
		$this->auth();		
		$this->init_setting();

		$posts = Request::all();
		$this->hook_before($posts);

		//Check if cache
		$cache    	= @$posts['cache'];
		$cachekey 	= md5('api_'.$this->permalink.'_'.json_encode($posts));
		if($cache && Cache::has($cachekey)) {
			$result = Cache::get($cachekey);
			$this->hook_after($posts,$result);
			$result['api_cache_mode'] = true;
			return response()->json($result);
		}

		$ac = DB::table('cms_apicustom')->where('permalink',$this->permalink)->first();
		if(!$ac) {
			$result['api_status'] = 0;
			$result['api_message'] = 'Sorry this API is not available, maybe has changed by admin, or please make sure api is correct.';
			goto show;
		}

		$tipe                = $ac->aksi;
		$table               = $ac->tabel;
		$limit               = ($posts['limit'])?:20;
		$offset              = ($posts['offset'])?:0;
		$orderby             = ($posts['orderby'])?:$table.'.id,desc';
		$groupby			 = ($posts['groupby'])?:'';
		$uploads_format_candidate = array(".jpg",".png",".gif",".bmp",".jpeg",".pdf",".xls",".xlsx",".doc",".docx",".zip",".rar",".7z");	
		$uploads_candidate 	 = array('photo','foto','image','picture','url_photo','url_foto','url_image','gambar','url_gambar','url_picture');
		$password_candidate  = array('pass','password','pin','pwd','passwd');		
		

		$cols_arr = @array_filter(explode(',',$ac->kolom));
		foreach($cols_arr as &$c) $c = trim($c);

		$result = array();
		unset($posts['debug']);
		unset($posts['cols']);
		unset($posts['limit']);
		unset($posts['offset']);
		unset($posts['orderby']);
		unset($posts['groupby']);
		unset($posts['token_code']);
		unset($posts['token_time']);
		unset($posts['cache']);


		$parameter = '';
		switch($tipe) {
			default:
			case 'detail':
			case 'list': $parameter = $ac->parameter; break;
			case 'save_add': $parameter = ($ac->parameter)?:$ac->kolom; break;
			case 'save_edit': $parameter = ($ac->parameter)?:$ac->kolom; break;
			case 'delete': $parameter = ($ac->parameter)?:$ac->kolom; break;
		}

		if($ac && $parameter && !$posts['bulk']) {

			$params = explode(',',$parameter);
			$posts_fields = array();

			foreach($params as &$p) $p = trim($p);

			if(count($posts)==0) {
				$result['api_status'] = 0;
				$result['api_message'] = "Please completed the parameters : ".implode(', ',$params);
				goto show;
			}

			foreach($posts as $k=>$v) {
				/*
				if(!in_array($k, $params)) {
					$result['api_status'] = 0;
					$result['api_message'] = 'Sorry parameter `'.$k.'` is not available in this API';
					goto show;
				}
				*/
				
				$posts_fields[] = $k;
			}

			$is_not_found_params = array();
			foreach($params as $p) {
				if(!in_array($p, $posts_fields)) {
					$is_not_found_params[] = $p;
				}
			}			

			$is_not_found_params = array_unique($is_not_found_params);
			if(count($is_not_found_params)) {
				$result['api_status'] = 0;
				$result['api_message'] = "These params are required : ".implode(', ',$is_not_found_params);
				goto show;
			}
		}

		$cols = DB::getSchemaBuilder()->getColumnListing($table);
				
		switch($tipe) {
			case 'list':		
				$data = DB::table($table);	 				
				$data->skip($offset);
				$data->take($limit);
				$data->addSelect(DB::raw("SQL_CALC_FOUND_ROWS $table.id"));
				foreach($cols as $col) {	
					$data->addSelect($table.'.'.$col);									
					if(substr($col,0,3)=='id_') {
						$jointable = substr($col,3);
						$jointable_field = DB::getSchemaBuilder()->getColumnListing($jointable);
						$data->leftjoin($jointable,$jointable.'.id','=',$table.'.'.$col);
						foreach($jointable_field as $jf) {
							$jf_alias = str_replace('_'.$jointable,'',$jf);
							$jf_alias = $jointable.'_'.$jf_alias;
							$data->addselect($jointable.'.'.$jf.' as '.$jf_alias);							
						}
					}
				}

				if($ac && $ac->sub_query_1) {
					$subquery = $ac->sub_query_1;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery); //add %%PARAM%% alias as parameter
					}
					$data->addSelect(DB::raw($subquery));
				}
				if($ac && $ac->sub_query_2) {
					$subquery = $ac->sub_query_2;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery);
					}
					$data->addSelect(DB::raw($subquery));
				}
				if($ac && $ac->sub_query_3) {
					$subquery = $ac->sub_query_3;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery);
					}
					$data->addSelect(DB::raw($subquery));
				}
				
				foreach($posts as $key => $val) {
					if(strpos($key,'search_')!==FALSE) {
						$key = str_replace('search_','',$key);
						if(!in_array($key, $cols)) continue;
						$data->where($table.'.'.$key,'like','%'.$val.'%');
					}else{						
						if(!in_array($key, $cols)) continue;
						
						if(is_array($val)) {
							$data->where(function($w) use ($val,$key) {
								$wi = 1;
								foreach($val as $v) {
									if($wi==1) $w->where($key,$v);
									else $w->orwhere($key,$v);

									$wi++;
								}								
							});
						}else{
							$data->where($table.'.'.$key,$val);	
						}						
					}
				}

				if($groupby) {
					$data->groupby($groupby);
				}

				//IF SQL WHERE IS NOT NULL
				if($ac->sql_where) $data->whereraw($ac->sql_where);

				$this->hook_query_list($data);
				
				$datar = array();
				if($orderby) {
					$orderby_raw = explode(',',$orderby);
					$orderby_col = $orderby_raw[0];
					$orderby_val = $orderby_raw[1];
				}else{
					$orderby_col = $table.'.id';
					$orderby_val = 'desc';
				}
				$rows = $data->orderby($orderby_col,$orderby_val)->get();		
				$rows = json_decode(json_encode($rows),true);	

				#echo '<pre>';
				#print_r(DB::getQueryLog());
				#exit;
								
				foreach($rows as $r) {					
					foreach($r as $k=>$v) {												
						foreach($uploads_format_candidate as $uff) {
							if( ((substr(strtolower($v),-3)==$uff) || (substr(strtolower($v),-4)==$uff) || (substr(strtolower($v),-5)==$uff)) && (substr(strtolower($v),4)!='http') ) {
									$r[$k] = asset($r[$k]);									
									break;
							}
						}	
						if($cols_arr && !in_array($k, $cols_arr)){
							unset($r[$k]);
							continue;
						}					
					}					
					$datar[] = $r;
				}


				$total_all_data = DB::select(DB::raw("SELECT FOUND_ROWS() AS Totalcount;"));
				$total_all_data = $total_all_data[0]->Totalcount;
				$result['api_status'] = 1;
				$result['api_message'] = 'success';
				$result['api_total_data'] = $total_all_data;
				$result['api_offset'] = $offset;
				$result['api_limit'] = $limit;
				$result['data'] = $datar;
			break;
			case 'html':
			case 'detail':

				$passworhash = '';

				$data = DB::table($table);
				foreach($cols as $col) {					
					$data->addSelect($table.'.'.$col);									
					if(substr($col,0,3)=='id_') {
						$jointable = substr($col,3);
						$jointable_field = DB::getSchemaBuilder()->getColumnListing($jointable);
						$data->leftjoin($jointable,$jointable.'.id','=',$table.'.'.$col);
						foreach($jointable_field as $jf) {
							$jf_alias = str_replace('_'.$jointable,'',$jf);
							$jf_alias = $jointable.'_'.$jf_alias;
							$data->addselect($jointable.'.'.$jf.' as '.$jf_alias);							
						}
					}
				}

				if($ac && $ac->sub_query_1) {
					$subquery = $ac->sub_query_1;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery); //add %%PARAM%% alias as parameter
					}
					$data->addSelect(DB::raw($subquery));
				}
				if($ac && $ac->sub_query_2) {
					$subquery = $ac->sub_query_2;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery);
					}
					$data->addSelect(DB::raw($subquery));
				}
				if($ac && $ac->sub_query_3) {
					$subquery = $ac->sub_query_3;
					foreach($posts as $key=>$val) {
						$subquery = str_replace("%%".$key."%%",$val,$subquery);
					}
					$data->addSelect(DB::raw($subquery));
				}

				foreach($posts as $key => $val) {					
					if(in_array($key, $password_candidate)) continue;

					if(strpos($key,'search_')!==FALSE) {
						$key = str_replace('search_','',$key);
						$data->where($table.'.'.$key,'like','%'.$val.'%');
						if(!in_array($key, $cols)) continue;
					}else{					
						if(!in_array($key, $cols)) continue;
						$data->where($table.'.'.$key,$val);
					}
				}

				//IF SQL WHERE IS NOT NULL
				if($ac->sql_where) $data->whereraw($ac->sql_where);		

				$this->hook_query_detail($data);		

				$data = (array) $data->first();
				
				foreach($data as $k=>$v) {
					
					if(in_array($k, $password_candidate)) {
						$password = @$posts[$k];
						if($password && !Hash::check($password,$v)) {
							$result['api_status']  = 0;
							$result['api_message'] = 'Sorry the password is incorrect !';
							goto show;
						}
					}	

					foreach($uploads_format_candidate as $uff) {
						if( ((substr(strtolower($v),-3)==$uff) || (substr(strtolower($v),-4)==$uff) || (substr(strtolower($v),-5)==$uff)) && (substr(strtolower($v),4)!='http') ) {
							$data[$k] = asset($data[$k]);
							break;
						} 
					}	

					if($cols_arr && !in_array($k, $cols_arr)){
						unset($data[$k]);
						continue;
					}			
				}


				if($tipe=='html') {
					foreach($cols_arr as $c) {
						echo "<p>".$data[$c]."</p>";
					}
					exit;
				}

				$result['api_status'] = ($data)?1:0;
				$result['api_message'] = ($data)?'success':'Sorry data not found !';
				$result = array_merge($result,$data);
			break;
			case 'save_add':							
				if($posts['bulk']) {
					$bulks = json_decode($posts['bulk'],true);
					foreach($bulks as $bulk) {
							$datas = array();
							foreach($bulk as $k=>$v) {	
														
								if(in_array($k, $password_candidate)) {
									if(!empty($v)) {
										$v = Hash::make($v);			
									}else{
										continue;
									}			
								}

								$datas[$k] = $v;																		
							}
							$insert = DB::table($table)->insert($datas);
							$lastInsertId[] = DB::getPdo()->lastInsertId();
					}
				}else{
					
					$datas = array();

					foreach($posts as $k=>$v) {

						if(in_array($k, $password_candidate)) {
							if(!empty($v)) {
								$v = Hash::make($v);			
							}else{
								continue;
							}			
						}						

						if(in_array($k, $uploads_candidate)) {
							$upload_mode = @$this->setting->upload_mode?:'file';
							$upload_path = @$this->setting->upload_path?:'uploads/';
							$name = $k;
							if (Request::hasFile($name))
							{			
								$file               = Request::file($name);
								$fm                 = array();
								$fm['name']         = $_FILES[$name]['name'];					
								$fm['ext']          = $file->getClientOriginalExtension();
								$fm['size']         = $_FILES[$name]['size'];
								$fm['content_type'] = $_FILES[$name]['type'];

								if($upload_mode=='database') {
									$fm['filedata']     = file_get_contents($_FILES[$name]['tmp_name']);
									DB::table('filemanager')->insert($fm);
									$id_fm              = DB::getPdo()->lastInsertId();
									DB::table('filemanager')->where('id',$id_fm)->update(['id_md5' =>md5($id_fm)]);
									$filename         	= 'upload_virtual/files/'.md5($id_fm).'.'.$fm['ext'];
								}else{
									if(!file_exists($upload_path.date('Y-m'))) {
										if(!mkdir($upload_path.date('Y-m'),0777)) {
											die('Gagal buat folder '.$upload_path.date('Y-m'));
										}
									}
									
									$filename = md5(str_random(12)).'.'.$fm['ext'];
									$file->move($upload_path.date('Y-m'),$filename);						
									$filename = $upload_path.date('Y-m').'/'.$filename;
								}
								$v	= $filename;  
							}
						}

						$datas[$k] = $v;
					}
					$insert = DB::table($table)->insert($datas);
					$lastInsertId = DB::getPdo()->lastInsertId();
				} 
				
				$result['api_status'] = ($insert)?1:0;
				$result['api_message'] = ($insert)?'The data has been added successfully':'Oops, added data was failed !';
				$result['id'] = $lastInsertId;
			break;
			case 'save_edit':
				
				$datas = array();
				foreach($posts as $k=>$v) {
					if(in_array($k, $password_candidate)) {
						if(!empty($v)) {
							$v = Hash::make($v);			
						}else{
							continue;
						}			
					}

					if(in_array($k, $uploads_candidate)) {
						$upload_mode = @$this->setting->upload_mode?:'file';
						$upload_path = @$this->setting->upload_path?:'uploads/';
						$name = $k;
						if (Request::hasFile($name))
						{			
							$file               = Request::file($name);
							$fm                 = array();
							$fm['name']         = $_FILES[$name]['name'];					
							$fm['ext']          = $file->getClientOriginalExtension();
							$fm['size']         = $_FILES[$name]['size'];
							$fm['content_type'] = $_FILES[$name]['type'];

							if($upload_mode=='database') {
								$fm['filedata']     = file_get_contents($_FILES[$name]['tmp_name']);
								DB::table('filemanager')->insert($fm);
								$id_fm              = DB::getPdo()->lastInsertId();
								DB::table('filemanager')->where('id',$id_fm)->update(['id_md5' =>md5($id_fm)]);
								$filename         	= 'upload_virtual/files/'.md5($id_fm).'.'.$fm['ext'];
							}else{
								if(!file_exists($upload_path.date('Y-m'))) {
									if(!mkdir($upload_path.date('Y-m'),0777)) {
										die('Gagal buat folder '.$upload_path.date('Y-m'));
									}
								}
								
								$filename = md5(str_random(12)).'.'.$fm['ext'];
								$file->move($upload_path.date('Y-m'),$filename);						
								$filename = $upload_path.date('Y-m').'/'.$filename;
							}
							$v	= $filename;  
						}
					}


					$datas[$k] = $v;
				}
				$update = DB::table($table);
				
				//IF SQL WHERE IS NOT NULL
				if($ac->sql_where) $update->whereraw($ac->sql_where);
				else $update->where('id',$posts['id']);

				$update = $update->update($datas);
				$result['api_status'] = ($update)?1:0;
				$result['api_message'] = ($update)?'The data has been saved successfully':'Oops, saved data was failed !';
			break;
			case 'delete':
				$delete = DB::table($table);
				foreach($posts as $key => $val) {
					if(!in_array($key, $cols)) continue;
					$delete->where($table.'.'.$key,$val);
				}

				//IF SQL WHERE IS NOT NULL
				if($ac->sql_where) $delete->whereraw($ac->sql_where);

				$delete = $delete->delete();
				$result['api_status'] = ($delete)?1:0;
				$result['api_message'] = ($delete)?'Delete data successfully':'failed, maybe id is not found at our database!';				
			break;
		}

		

		show:
		$result['api_status']  = $this->hook_api_status?:$result['api_status'];
		$result['api_message'] = $this->hook_api_message?:$result['api_message'];
		$this->hook_after($posts,$result);

		if($cache) {
			Cache::put($cachekey,$result,$cache);
		}

		return response()->json($result);
	}

	private function init_setting() {
		$setting = DB::table('cms_settings')->get();
		$setting_array = array();
		foreach($setting as $set) {
			$setting_array[$set->name] = $set->content;
		}
		$this->setting = json_decode(json_encode($setting_array));
	}


	public function getMultiple() {

		$test_total = 1;
		$rows = array();
		
		echo "<p style='font-weight:bold'>Example Post Multiple Parameter : </p>";

		for($i=1;$i<=$test_total;$i++) {
			$data[$i] = array(
				"api_table"=>"order",
				"tanggal"=>date('Y-m-d H:i:s'),
				'id_member'=>1,
				'subtotal_harga'=>1000*$i,
				'total_biaya_kirim'=>1000*$i,
				'grandtotal_harga'=>2000*$i,
				'status'=>'new'
			); 

			for($ii=1;$ii<=$test_total;$ii++) {
			$data[$i]['data'][$ii] = array(
							"api_table"=>"order_address",
							"api_field_parent"=>"id_order",
							'nama_penerima'=>'FERRY '.$ii,
							'provinsi'=>'PROVINSI '.$ii,
							'kota'=>'KOTA '.$ii,
							'alamat'=>'ALAMAT '.$ii,
							'telp'=>'123456 '.$ii
						);

				for($iii=1;$iii<=$test_total;$iii++) {
					$data[$i]['data'][$ii]['data'][$iii] = array(
						"api_table"=>"order_detail",
						"api_field_parent"=>"id_order,id_order_address",
						"nama_produk"=>"PRODUK ".$iii,
						"qty"=>1*$iii,
						"harga"=>1000*$iii,
						"biaya_kirim"=>1000*$iii,
						"subtotal"=>2000*$iii,
						"keterangan"=>"test ".$iii,
						"status"=>"new"
					);
				}


			}
		}

		$rows['data'] = $data;
		$json = json_encode($rows,JSON_PRETTY_PRINT);
		echo '<pre>';
		print_r($json);
	}

	public function postMultiple() {
		$this->auth();
		
		$data = Request::input('data');
		$this->insertDataApi($data);

		$result = array();
		$result['api_status'] = 1;
		$result['api_message'] = 'success';
		return response()->json($result);
	}

	private function insertDataApi($data) {
		foreach($data as $ds) {
			$table = $ds['api_table'];
			@$subdata = $ds['data'];

			$fields = explode(',',$ds['api_field_parent']);
			foreach($fields as $f) {
				@$ds[$f] = $this->last_id_tmp[$f];
			}
			unset($ds['api_table']);
			unset($ds['api_table']);
			unset($ds['api_field_parent']);
			unset($ds['data']);
			$ds = array_filter($ds);
			DB::table($table)->insert($ds);
			$id = $this->last_id_tmp['id_'.$table] = DB::getPdo()->lastInsertId();
			echo "<p>Insert $table with id ".$id."</p>";
			echo "<pre>";
			print_r($ds);
			echo "</pre>";
			if($subdata) $this->insertDataApi($subdata);
		}
	}
 

	public function auth() {
		$screetkey = @unserialize(str_replace("* WARNING !! DO NOT REMOVE THIS BELLOW KEY *\n","",strip_tags(file_get_contents("resources/_token"))))['key'];		

		if($this->setting->api_debug_mode=='false') {

			$result = array();
			$validator = Validator::make(
				[	
				'screetkey'=>$screetkey,
				'X-Authorization-Token'=> Request::header('X-Authorization-Token'),
				'X-Authorization-Time'=>Request::header('X-Authorization-Time')	
				],			
				[
				'screetkey'=>'required',
				'X-Authorization-Token'=>'required',
				'X-Authorization-Time'=>'required',					
				]
			);		
			
			if ($validator->fails()) 
			{
				$message = $validator->errors()->all();			
				$result['api_status'] = 0;
				$result['api_message'] = implode(', ',$message);			
				$res = response()->json($result,400);
				$res->send();
				exit;
			}

			//verifikasi trust token
			if(md5($screetkey.Request::header('X-Authorization-Time')) != Request::header('X-Authorization-Token')) {			
				$result['api_status']	= 0;
				$result['api_message'] 	= "INVALID TOKEN";
				$res = response()->json($result,401);
				$res->send();
				exit;
			}

		}	//end debug			
	}

	

}




