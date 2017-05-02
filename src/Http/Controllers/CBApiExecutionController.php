<?php
namespace Crocodicstudio\Crudbooster\Http\Controllers;

error_reporting(E_ALL ^ E_NOTICE);

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
use CRUDBooster;

class CBApiExecutionController extends Controller
{
	public $methodType;
	public $permalink;
	public $hookApiStatus;
	public $hookApiMessage;
	public $lastIDTemp = array();

	public function hookBefore(&$postData)
	{

	}

	public function hookAfter($postData,&$result)
	{

	}

	public function hookQuery(&$query)
	{

	}

	public function hookApiStatus($boolean=false)
	{
		$this->hookApiStatus = $boolean;
	}

	public function hookApiMessage($message)
	{
		$this->hookApiMessage = $message;
	}

	public function run()
	{								
		$posts      = Request::all();
		$postKeys   = array_keys($posts);
		$postValues = array_values($posts);

		$row        = CB::first('cb_api',['permalink'=>$this->permalink]);		
		$actionType = $row_api->action_type;
		$table      = $row_api->table;

		$debugMessage = 'You are in debug mode !';

		/*
		| ----------------------------------------------
		| Method Type validation
		| ----------------------------------------------
		|
		*/
		if ($row->method_type) {
			$methodType = $row->method_type;
			if ($methodType) {
				if (!Request::isMethod($methodType)) {
					$result['api_status'] = 0;
					$result['api_message'] = "The request method is not allowed !";
					goto show;
				}
			}
		}

		/*
		| ----------------------------------------------
		| Check the row is exists or not
		| ----------------------------------------------
		|
		*/
		if (!$row) {
			$result['api_status']  = 0;
			$result['api_message'] = 'Sorry this API is no longer available, maybe has changed by admin, or please make sure api url is correct.';
			goto show;
		}

		$params = unserialize($row->params);
		$responses = unserialize($row->responses);

		/*
		| ----------------------------------------------
		| User Data Validation
		| ----------------------------------------------
		|
		*/
		if($params) {
			$typeException = ['password','ref','base64_file','custom','search'];
			$inputValidator = array();
			$dataValidator = array();
			foreach($params as $param) {
				$name     = $param['name'];
				$type     = $param['type'];
				$value    = $posts[$name];

				$required = $param['required'];
				$config   = $param['config'];
				$used     = $param['used'];
				$formatValidation = array();

				if($used == 0) continue;

				if($param['config'] && substr($param['config'], 0, 1) != '*') continue;

				if($required) {
					$formatValidation[] = 'required';
				}

				if($type == 'exists') {
					$config = explode(',', $config);
					$existsTable = $config[0];
					$existsTable = CRUDBooster::parseSqlTable($existsTable)['table'];
					$existsField = $config[1];
					$config = ($field)?$existsTable.','.$existsField:$existsTable;
					$formatValidation[] = 'exists:'.$config;
				}elseif ($type == 'unique') {
					$config = explode(',', $config);
					$uniqueTable = $config[0];
					$uniqueTable = CRUDBooster::parseSqlTable($uniqueTable)['table'];
					$uniquefield = $config[1];
					$config = ($uniquefield)?$uniqueTable.','.$uniquefield:$uniqueTable;
					$formatValidation[] = 'unique:'.$config;
				}elseif ($type == 'date_format') {
					$formatValidation[] = 'date_format:'.$config;
				}elseif ($type == 'digits_between') {
					$formatValidation[] = 'digits_between:'.$config;
				}elseif ($type == 'in') {
					$formatValidation[] = 'in:'.$config;
				}elseif ($type == 'mimes') {
					$formatValidation[] = 'mimes:'.$config;
				}elseif ($type == 'min') {
					$formatValidation[] = 'min:'.$config;
				}elseif ($type == 'max') {
					$formatValidation[] = 'max:'.$config;
				}elseif ($type == 'not_in') {
					$formatValidation[] = 'not_in:'.$config;
				}else{
					if(!in_array($type, $typeException)) {
						$formatValidation[] = $type;
					}
				}

				if($name == 'id') {
					$existsTable = CRUDBooster::parseSqlTable($table)['table'];
					$formatValidation[] = 'exists:'.$existsTable.',id';
				}						

				$inputValidator[$name] = $value;
				$dataValidator[$name] = implode('|',$formatValidation);
			}

			$validator = Validator::make($inputValidator,$dataValidator);
		    if ($validator->fails()) {
		        $message = $validator->errors()->all();
		        $message = implode(', ',$message);
		        $result['api_status'] = 0;
		        $result['api_message'] = $message;
		        goto show;
		    }
		}

		$responseFields = array();
		foreach($responses as $resp) {
			if ($resp['used']) $responseFields[] = $resp['name'];
		}

		$this->hookBefore($posts);

		$limit                   = ($posts['limit'])?:20;
		$offset                  = ($posts['offset'])?:0;
		$orderby                 = ($posts['orderby'])?:$table.'.id,desc';
		$uploadTypes             = explode(',',config('crudbooster.UPLOAD_TYPES'));
		$imageFieldCandidates    = explode(',',config('crudbooster.IMAGE_FIELDS_CANDIDATE'));
		$passwordFieldCandidates = explode(',',config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
		$asset                   = asset('/');

		unset($posts['limit']);
		unset($posts['offset']);
		unset($posts['orderby']);

		if ($actionType == 'list' || $actionType == 'detail' || $actionType == 'delete') {
			$nameTemp = array();
			$data = DB::table($table);
			$data->skip($offset);
			$data->take($limit);
			foreach($responses as $resp) {
				$name = $resp['name'];
				$type = $resp['type'];
				$subquery = $resp['subquery'];
				$used = intval($resp['used']);

				if ($used == 0 && !CRUDBooster::isForeignKey($name)) continue;

				if (in_array($name, $nameTemp)) continue;

				if ($name == 'ref_id') continue;

				if ($type=='custom') continue;

				if ($subquery) {
					$data->addSelect(DB::raw(
						'('.$subquery.') as '.$name
						));
					$nameTemp[] = $name;
					continue;
				}

				if ($used) {
					$data->addSelect($table.'.'.$name);
				}

				$nameTemp[] = $name;
				if (CRUDBooster::isForeignKey($name)) {
					$joinTable = CRUDBooster::getTableForeignKey($name);
					$joinTableField = CRUDBooster::getTableColumns($joinTable);

					$data->leftjoin($joinTable,$joinTable.'.id','=',$table.'.'.$name);
					foreach($joinTableField as $joinField) {
						$joinFieldAs = $joinTable.'_'.$joinField;
						if (in_array($joinFieldAs, $responseFields)) {
							$data->addselect($joinTable.'.'.$joinField.' as '.$joinFieldAs);
							$nameTemp[] = $joinFieldAs;
						}
					}
				}
			} //End Responses

			foreach($params as $param) {
				$name     = $param['name'];
				$type     = $param['type'];
				$value    = $posts[$name];
				$used     = $param['used'];
				$required = $param['required'];
				$config = $param['config'];

				if ($type == 'password') {
					$data->addselect($table.'.'.$name);
				}

				if ($type == 'search') {
					$searchIN = explode(',',$config);

					if ($required == '1') {
						$data->where(function($w) use ($searchIN,$value) {
							foreach($searchIN as $k=>$field) {
								if($k==0) $w->where($field,"like","%$value%");
								else $w->orWhere($field,"like","%$value%");
							}
						});
					} else {
						if ($used) {
							if ($value) {
								$data->where(function($w) use ($searchIN,$value) {
									foreach($searchIN as $k=>$field) {
										if($k==0) $w->where($field,"like","%$value%");
										else $w->orWhere($field,"like","%$value%");
									}
								});
							}
						}
					}
				}
			}

			if (CRUDBooster::isColumnExists($table,'deleted_at')) {
				$data->where($table.'.deleted_at',NULL);
			}

			$data->where(function($w) use ($params,$posts,$table,$typeException) {
				foreach($params as $param) {
					$name     = $param['name'];
					$type     = $param['type'];
					$value    = $posts[$name];
					$used     = $param['used'];
					$required = $param['required'];

					if (in_array($type, $typeException)) {
						continue;
					}

					if ($param['config']!='' && substr($param['config'], 0,1) != '*') {
						$value = $param['config'];
					}

					if ($required == '1') {
						if(CRUDBooster::isColumnExists($table,$name)) {
							$w->where($table.'.'.$name,$value);							
						}else{
							$w->having($name,'=',$value);
						}
					} else {
						if ($used) {
							if ($value) {
								if (CRUDBooster::isColumnExists($table,$name)) {
									$w->where($table.'.'.$name,$value);
								} else {
									$w->having($name,'=',$value);
								}
							}
						}
					}
				}
			});

			//IF SQL WHERE IS NOT NULL
			if ($row->sql_where) {
				$data->whereRaw($row->sql_where);
			}

			$this->hookQuery($data);

			if ($actionType == 'list') {
				if ($orderby) {
					$orderbyRaw = explode(',',$orderby);
					$orderbyField = $orderbyRaw[0];
					$orderbyValue = $orderbyRaw[1];
				} else {
					$orderbyField = $table.'.id';
					$orderbyValue = 'desc';
				}

				$rows = $data->orderby($orderbyField,$orderbyValue)->get();

				if ($rows) {

					foreach($rows as &$row) {
						foreach($row as $k=>$v) {
							$ext = \File::extension($v);
							if (in_array($ext, $uploadTypes)) {
								$row->$k = asset($v);
							}

							if (!in_array($k,$responseFields)) {
								unset($row[$k]);
							}
						}
					}

					$result['api_status'] = 1;
					$result['api_message'] = 'success';
					if (CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}		
					$result['data'] = $rows;
				} else {
					$result['api_status']  = 0;
					$result['api_message'] = 'There is no data found !';
					if (CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}
					$result['data'] = array();
				}
			} elseif ($actionType == 'detail') {

				$rows = $data->first();

				if ($rows) {

					foreach($params as $param) {
						$name     = $param['name'];
						$type     = $param['type'];
						$value    = $posts[$name];
						$used     = $param['used'];
						$required = $param['required'];

						if ($param['config']!='' && substr($param['config'], 0,1) != '*') {
							$value = $param['config'];
						}

						if ($required) {
							if ($type == 'password') {
								if (!Hash::check($value,$rows->{$name})) {
									$result['api_status'] = 0;
									$result['api_message'] = 'Your password is wrong !';
									if (CRUDBooster::getSetting('api_debug_mode')=='true') {
										$result['api_authorization'] = $debugMessage;
									}
									goto show;
								}
							}
						} else {
							if ($used) {
								if ($value) {
									if (!Hash::check($value,$row->{$name})) {
										$result['api_status'] = 0;
										$result['api_message'] = 'Your password is wrong !';
										if (CRUDBooster::getSetting('api_debug_mode')=='true') {
											$result['api_authorization'] = $debugMessage;
										}
										goto show;
									}
								}
							}
						}
					}

					foreach($rows as $k=>$v) {
						$ext = \File::extension($v);

						if (in_array($ext, $uploadTypes)) {
							$rows->$k = asset($v);
						}

						if (!in_array($k,$responseFields)) {
							unset($row[$k]);
						}
					}

					$result['api_status']  = 1;
					$result['api_message'] = 'success';

					if (CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}

					$rows     = (array) $rows;
					$result   = array_merge($result,$rows);

				} else {

					$result['api_status']  = 0;
					$result['api_message'] = 'There is no data found !';

					if (CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}

				}
			} elseif ($actionType == 'delete') {

				if (CRUDBooster::isColumnExists($table,'deleted_at')) {
					$delete = $data->update(['deleted_at'=>date('Y-m-d H:i:s')]);
				} else {
					$delete = $data->delete();
				}

				$result['api_status'] = ($delete)?1:0;
				$result['api_message'] = ($delete)?'success':'failed';
				if (CRUDBooster::getSetting('api_debug_mode')=='true') {
					$result['api_authorization'] = $debugMessage;
				}

			}

		} elseif ($actionType == 'save_add' || $actionType == 'save_edit') {

		    $rowAssign = array();
		    foreach($inputValidator as $k=>$v) {
		    	if (CRUDBooster::isColumnExists($table,$k)) {
		    		$rowAssign[$k] = $v;
		    	}
		    }

		    foreach($params as $param) {
		    	$name = $param['name'];
		    	$used = $param['used'];
		    	$value = $posts[$name];
		    	if ($used=='1' && $value=='') {
		    		unset($rowAssign[$name]);
		    	}
		    }

		    if ($actionType == 'save_add') {
		    	if (CRUDBooster::isColumnExists($table,'created_at')) {
		    		$rowAssign['created_at'] = date('Y-m-d H:i:s');
		    	}
		    } 

		    if ($actionType == 'save_edit') {
		    	if (CRUDBooster::isColumnExists($table,'updated_at')) {
		    		$rowAssign['updated_at'] = date('Y-m-d H:i:s');
		    	}
		    }

		    $rowAssignKeys = array_keys($rowAssign);

		    foreach($params as $param) {
		    	$name = $param['name'];
		    	$value = $posts[$name];
		    	$config = $param['config'];
		    	$type = $param['type'];
		    	$required = $param['required'];
		    	$used = $param['used'];

		    	if (!in_array($name, $rowAssignKeys)) {
					continue;
				}

				if (substr($param['config'], 0,1) != '*') {
					$value = $param['config'];
				}

		    	if ($type == 'file' || $type == 'image') {
		    		if (Request::hasFile($name))
					{
						$file = Request::file($name);
						$ext  = $file->getClientOriginalExtension();

						//Create Directory Monthly
						Storage::makeDirectory(date('Y-m'));

						//Move file to storage
						$filename = md5(str_random(5)).'.'.$ext;
						if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {
							$v = 'uploads/'.date('Y-m').'/'.$filename;
							$rowAssign[$name] = $v;
						}
					}
		    	} elseif ($type == 'base64_file') {
		    		$fileData = base64_decode($value);
					$f = finfo_open();
					$mimeType = finfo_buffer($f, $fileData, FILEINFO_MIME_TYPE);
					@$mimeType = explode('/',$mimeType);
					@$mimeType = $mimeType[1];
					if ($mimeType) {
						if (in_array($mimeType, $uploads_format_candidate)) {
							Storage::makeDirectory(date('Y-m'));
							$filename = md5(str_random(5)).'.'.$mimeType;
							if (file_put_contents(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')).'/'.$filename, $fileData)) {
								$v = 'uploads/'.date('Y-m').'/'.$filename;
								$rowAssign[$name] = $v;
							}
						}
					}
		    	} elseif ($type == 'password') {
		    		$rowAssign[$name] = Hash::make($value);
		    	}

		    }

		    //Make sure if saving/updating data additional param included
		    $arrkeys = array_keys($rowAssign);      
		    foreach($posts as $key => $value) {
		        if(!in_array($key, $arrkeys)) {
		          $rowAssign[$key] = $value;
		        }
		    }


		    if ($actionType == 'save_add') {

		    	$rowAssign['id'] = CB::newId($table);
		    	DB::table($table)->insert($rowAssign);
		    	$result['api_status']  = ($rowAssign['id'])?1:0;
				$result['api_message'] = ($rowAssign['id'])?'success':'failed';

				if(CRUDBooster::getSetting('api_debug_mode')=='true') {
					$result['api_authorization'] = $debugMessage;
				}

				$result['id'] = $rowAssign['id'];

		    } else {

		    	try {
		    		$update = DB::table($table);

				    $update->where($table.'.id',$rowAssign['id']);

				    if($row_api->sql_where) {
				    	$update->whereraw($row_api->sql_where);
				    }			    

				    $this->hook_query($update);

				    $update = $update->update($rowAssign);
					$result['api_status']  = 1;
					$result['api_message'] = 'success';
					if(CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}
		    	} catch (\Exception $e) {
		    		$result['api_status']  = 0;
					$result['api_message'] = 'failed, '.$e;
					if(CRUDBooster::getSetting('api_debug_mode')=='true') {
						$result['api_authorization'] = $debugMessage;
					}
		    	}

		    }

		    // Update The Child Table
		    foreach($params as $param) {
		    	$name = $param['name'];
		    	$value = $posts[$name];
		    	$config = $param['config'];
		    	$type = $param['type'];
		    	if ($type == 'ref') {
		    		if (CRUDBooster::isColumnExists($config,'id_'.$table)) {
		    			DB::table($config)->where($name,$value)->update(['id_'.$table=>$lastId]);
		    		} elseif (CRUDBooster::isColumnExists($config,$table.'_id')) {
		    			DB::table($config)->where($name,$value)->update([$table.'_id'=>$lastId]);
		    		}
		    	}
		    }
		}


		show:
		$result['api_status']  = $this->hookApiStatus?:$result['api_status'];
		$result['api_message'] = $this->hookApiMessage?:$result['api_message'];

		if (CRUDBooster::getSetting('api_debug_mode')=='true') {
			$result['api_authorization'] = $debugMessage;
		}				

		$this->hookAfter($posts,$result);

		return response()->json($result);
	}
	
}




