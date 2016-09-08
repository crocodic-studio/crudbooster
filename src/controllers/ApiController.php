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

class ApiController extends Controller {
	
	

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

	public function execute_api() {
		auth_api();
		
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
			$result['api_message'] = 'Sorry this API is no longer available, maybe has changed by admin, or please make sure api is correct.';
			goto show;
		}

		$tipe                = $ac->aksi;
		$table               = $ac->tabel;
		$limit               = ($posts['limit'])?:20;
		$offset              = ($posts['offset'])?:0;
		$orderby             = ($posts['orderby'])?:$table.'.id,desc';
		$groupby			 = ($posts['groupby'])?:'';
		$uploads_format_candidate = explode(',',config("crudbooster.UPLOAD_TYPES"));	
		$uploads_candidate 	 = explode(',',config('crudbooster.IMAGE_FIELDS_CANDIDATE'));
		$password_candidate  = explode(',',config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));		
			
		foreach($uploads_format_candidate as &$up) $up = '.'.$up;

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
							$name = $k;
							if (Request::hasFile($name))
							{			
								$file = Request::file($name);					
								$ext  = $file->getClientOriginalExtension();

								//Create Directory Monthly 
								Storage::makeDirectory(date('Y-m'));

								//Move file to storage
								$filename = md5(str_random(5)).'.'.$ext;
								if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {						
									$v = 'uploads/'.date('Y-m').'/'.$filename;
								}					  
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
						
						$name = $k;
						if (Request::hasFile($name))
							{			
								$file = Request::file($name);					
								$ext  = $file->getClientOriginalExtension();

								//Create Directory Monthly 
								Storage::makeDirectory(date('Y-m'));

								//Move file to storage
								$filename = md5(str_random(5)).'.'.$ext;
								if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {						
									$v = 'uploads/'.date('Y-m').'/'.$filename;
								}					  
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

	

}




