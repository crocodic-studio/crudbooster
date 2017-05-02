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
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use CB;
use Schema;

class CBController extends Controller
{		
	public $table;
	public $titleField;
	public $primaryKey         = 'id';
	public $inputAssignmentData = array();
	public $columns            = array();
	public $inputs             = array();
	public $data               = array();
	public $addAction          = array();
	public $orderBy            = NULL;
	public $passwordCandidates = NULL;
	public $dateCandidates     = NULL;
	public $limit              = 20;
	public $globalRoles        = FALSE;
	public $showNumbering      = FALSE;

	public $alert       = array();
	public $indexButton = array();

	public $buttonFilter      = TRUE;
	public $buttonExport      = TRUE;
	public $buttonImport      = TRUE;
	public $buttonShow        = TRUE;
	public $buttonAddMore     = TRUE;
	public $buttonTableAction = TRUE;
	public $buttonBulkAction  = TRUE;
	public $buttonAdd         = TRUE;
	public $buttonDelete      = TRUE;
	public $buttonCancel      = TRUE;
	public $buttonSave        = TRUE;
	public $buttonEdit        = TRUE;
	public $buttonDetail      = TRUE;
	public $buttonActionStyle = 'button_icon';
	public $buttonActionWidth = NULL;
	public $subModule 		  = NULL;
	public $indexStatistic      = array();
	public $indexAdditionalView = array();
	public $preIndexHtml        = null;
	public $postIndexHtml       = null;
	public $loadJS              = array();
	public $loadCSS             = array();
	public $scriptJS            = NULL;
	public $styleCSS            = NULL;	
	public $showAddAction       = TRUE;
	public $tableRowColor       = array();
	public $buttonSelected      = array();
	public $returnURL           = NULL;
	public $parentField         = NULL;
	public $parentID            = NULL;
	public $hiddenInputs        = array();
	public $indexReturnMode     = FALSE; //for export

	private function shareProperties()
	{
		$this->cbInit();

		$this->checkHiddenInputs();
		
		$this->data['inputs']              = $this->inputs;
		$this->data['hiddenInputs']        = $this->hiddenInputs;
		$this->data['addAction']           = ($this->showAddAction)?$this->addAction:NULL;
		$this->data['table']               = $this->table;
		$this->data['titleField']          = $this->titleField;				
		$this->data['indexButton']         = $this->indexButton;
		$this->data['showNumbering']       = $this->showNumbering;
		$this->data['buttonDetail']        = $this->buttonDetail;
		$this->data['buttonEdit']          = $this->buttonEdit;
		$this->data['buttonShow']          = $this->buttonShow;
		$this->data['buttonAdd']           = $this->buttonAdd;
		$this->data['buttonDelete']        = $this->buttonDelete;
		$this->data['buttonFilter']        = $this->buttonFilter;
		$this->data['buttonExport']        = $this->buttonExport;
		$this->data['buttonAddMore']       = $this->buttonAddMore;
		$this->data['buttonCancel']        = $this->buttonCancel;
		$this->data['buttonSave']          = $this->buttonSave;
		$this->data['buttonTableAction']   = $this->buttonTableAction;
		$this->data['buttonBulkAction']    = $this->buttonBulkAction;
		$this->data['buttonImport']        = $this->buttonImport;
		$this->data['buttonActionWidth']   = $this->buttonActionWidth;
		$this->data['buttonSelected']      = $this->buttonSelected;
		$this->data['indexStatistic']      = $this->indexStatistic;
		$this->data['indexAdditionalView'] = $this->indexAdditionalView;
		$this->data['tableRowColor']       = $this->tableRowColor;
		$this->data['preIndexHtml']        = $this->preIndexHtml;
		$this->data['postIndexHtml']       = $this->postIndexHtml;
		$this->data['loadJS']              = $this->loadJS;
		$this->data['loadCSS']             = $this->loadCSS;
		$this->data['scriptJS']            = $this->scriptJS;
		$this->data['styleCSS']            = $this->styleCSS;		
		$this->data['parentField']         = (g('parentField'))?:$this->parentField;
		$this->data['parentID']            = (g('parentID'))?:$this->parentID;

        view()->share($this->data);
	}

	public function __call($method,$arguments) 
	{
        if(method_exists($this, $method)) {
            $this->shareProperties();
            return call_user_func_array(array($this,$method),$arguments);
        }
    }

	private function checkHiddenInputs()
	{
		if (count($this->hiddenInputs)) {
			foreach ($this->inputs as $i=>$input) {
				if (in_array($input['name'], $this->hiddenInputs)) {
					unset($this->inputs[$i]);
				}
			}
		}
	}

	public function getIndex()
	{		
		if (!CB::canView() && $this->globalRoles==FALSE) {
			CB::insertLog(trans('crudbooster.log_try_view',['module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		if (Request::get('parentTable')) {
			$parentTablePk = CB::findPrimaryKey(Request::get('parentTable'));
			$data['parentTable'] = DB::table(Request::get('parentTable'))->where($parentTablePk,Request::get('parentId'))->first();
			if (Request::get('foreignKey')) {
				$data['parentField'] = Request::get('foreignKey');
			} else {
				$data['parentField'] = $parentField = CB::getForeignKey(g('parentTable'),$this->table);				
			}

			if ($parentField) {
				foreach($this->columns as $i=>$column) {
					if ($column['name'] == $parentField) {
						unset($this->columns[$i]);
					}
				}
			}
		}
		
		$data['pageTitle']       = CB::getCurrentModule()->name;
		$data['pageDescription'] = trans('crudbooster.default_module_description');
		$data['dateCandidate']   = config('crudbooster.date_fields_candidate');
		$limit                   = (Request::get('limit'))?Request::get('limit'):$this->limit;
		$data['limit']           = $limit;

		$tableColumns = CB::getTableColumns($this->table);
		$result = DB::table($this->table)->select(DB::raw($this->table.".".$this->primaryKey));

		if (Request::get('parentId')) {
			$tableParent = $this->table;
			$tableParent = CB::parseSqlTable($tableParent)['table'];
			$result->where($tableParent.'.'.Request::get('foreignKey'),Request::get('parentId'));
		}

		$this->hookQueryIndex($result);

		if(in_array('deleted_at', $tableColumns)) {
			$result->where($this->table.'.deleted_at',NULL);
		}

		$alias          = array();
		$joinAliasCount = 0;
		$joinTableTemp  = array();
		$table          = $this->table;
		$columnsTable   = $this->columnsTable;
		foreach ($columnsTable as $index => $coltab) {

			$join = @$coltab['join'];
			$joinWhere = @$coltab['joinWhere'];
			$joinId = @$coltab['joinId'];
			$field = @$coltab['name'];
			$joinTableTemp[] = $table;

			if (!$field) throw new Exception("Please make sure there is key `name` in each row of col", 1);			

			if (strpos($field, ' as ')!==FALSE) {
				$field = substr($field, strpos($field, ' as ')+4);
				$result->addselect(DB::raw($coltab['name']));
				$columnsTable[$index]['typeData']   = 'varchar';
				$columnsTable[$index]['field']      = $field;
				$columnsTable[$index]['fieldRaw']   = $field;
				$columnsTable[$index]['fieldWith']  = $field;
				$columnsTable[$index]['isSubquery'] = true;
				continue;
			}

			if (strpos($field,'.')!==FALSE) {
				$result->addselect($field);
			} else {
				$result->addselect($table.'.'.$field);
			}

			$fieldArray = explode('.', $field);

			if (isset($fieldArray[1])) {
				$field = $fieldArray[1];
				$table = $fieldArray[0];
			}

			if ($join) {

				$joinExp     = explode(',', $join);

				$joinTable  = $joinExp[0];
				$joinTablePk = CB::findPrimaryKey($joinTable);
				$joinColumn = $joinExp[1];
				$joinAlias  = str_replace(".", "_", $joinTable);

				if (in_array($joinTable, $joinTableTemp)) {
					$joinAliasCount += 1;
					$joinAlias = $joinTable.$joinAliasCount;
				}
				$joinTableTemp[] = $joinTable;

				$result->leftjoin($joinTable.' as '.$joinAlias,$joinAlias.(($joinId)? '.'.$joinId:'.'.$joinTablePk),'=',DB::raw($table.'.'.$field. (($joinWhere) ? ' AND '.$joinWhere.' ':'') ) );
				$result->addselect($joinAlias.'.'.$joinColumn.' as '.$joinAlias.'_'.$joinColumn);

				$joinTableColumns = CB::getTableColumns($joinTable);

				if ($joinTableColumns) {
					foreach($joinTableColumns as $jtc) {
						$result->addselect($joinAlias.'.'.$jtc.' as '.$joinAlias.'_'.$jtc);
					}
				}

				$alias[] = $joinAlias;
				$columnsTable[$index]['typeData']  = CB::getFieldType($joinTable,$joinColumn);
				$columnsTable[$index]['field']     = $joinAlias.'_'.$joinColumn;
				$columnsTable[$index]['fieldWith'] = $joinAlias.'.'.$joinColumn;
				$columnsTable[$index]['fieldRaw']  = $joinColumn;

				@$joinTable1  = $joinExp[2];
				@$joinColumn1 = $joinExp[3];
				@$joinAlias1  = $joinTable1;

				if ($joinTable1 && $joinColumn1) {

					if (in_array($joinTable1, $joinTableTemp)) {
						$joinAliasCount += 1;
						$joinAlias1 = $joinTable1.$joinAliasCount;
					}

					$joinTableTemp[] = $joinTable1;

					$result->leftjoin($join_table1.' as '.$joinAlias1,$joinAlias1.'.id','=',$joinAlias.'.'.$joinColumn);
					$result->addselect($joinAlias1.'.'.$joinColumn1.' as '.$joinColumn1.'_'.$joinAlias1);
					$alias[] = $joinAlias1;
					$columnsTable[$index]['typeData']  = CB::getFieldType($join_table1,$joinColumn1);
					$columnsTable[$index]['field']     = $joinColumn1.'_'.$joinAlias1;
					$columnsTable[$index]['fieldWith'] = $joinAlias1.'.'.$joinColumn1;
					$columnsTable[$index]['fieldRaw']  = $joinColumn1;
				}

			} else {

				$result->addselect($table.'.'.$field);
				$columnsTable[$index]['typeData']  = CB::getFieldType($table,$field);
				$columnsTable[$index]['field']      = $field;
				$columnsTable[$index]['fieldRaw']  = $field;
				$columnsTable[$index]['fieldWith'] = $table.'.'.$field;
			}
		}

		if (Request::get('q')) {
			$result->where(function($w) use ($columnsTable, $request) {
				foreach ($columnsTable as $col) {
						if(!$col['fieldWith']) continue;
						if($col['isSubquery']) continue;
						$w->orwhere($col['fieldWith'],"like","%".Request::get("q")."%");
				}
			});
		}

		if (Request::get('where')) {
			foreach (Request::get('where') as $k=>$v) {
				$result->where($table.'.'.$k,$v);
			}
		}

		$filterIsOrderby = false;
		if (Request::get('filterColumn')) {

			$filterColumn = Request::get('filterColumn');
			$result->where(function($w) use ($filterColumn,$fc) {
				foreach ($filter_column as $key=>$fc) {

					$value = @$fc['value'];
					$type  = @$fc['type'];

					if($value=='' || $type=='') continue;

					if($type == 'between') continue;

					switch($type) {
						default:
							if($key && $type && $value) $w->where($key,$type,$value);
						break;
						case 'like':
						case 'not like':
							$value = '%'.$value.'%';
							if($key && $type && $value) $w->where($key,$type,$value);
						break;
						case 'in':
						case 'not in':
							if($value) {
								$value = explode(',',$value);
								if($key && $value) $w->whereIn($key,$value);
							}
						break;
					}
				}
			});

			foreach($filterColumn as $key=>$fc) {
				$value = @$fc['value'];
				$type  = @$fc['type'];
				$sorting = @$fc['sorting'];

				if ($sorting!='') {
					if ($key) {
						$result->orderby($key,$sorting);
						$filterIsOrderby = true;
					}
				}

				if ($type=='between') {
					if ($key && $value) $result->whereBetween($key,$value);
				} else {
					continue;
				}
			}
		}

		if ($filterIsOrderby == true) {

			$data['result']  = $result->paginate($limit);

		} else {
			if ($this->orderBy) {
				if (is_array($this->orderBy)) {
					foreach ($this->orderBy as $k=>$v) {
						if (strpos($k, '.')!==FALSE) {
							$orderbyTable = explode(".",$k)[0];
						} else {
							$orderbyTable = $table;
						}
						$result->orderby($orderbyTable.'.'.$k,$v);
					}
				}else{
					$this->orderBy = explode(";",$this->orderBy);
					foreach($this->orderBy as $o) {
						$o = explode(",",$o);
						$k = $o[0];
						$v = $o[1];
						if(strpos($k, '.')!==FALSE) {
							$orderbyTable = explode(".",$k)[0];
						}else{
							$orderbyTable = $table;
						}
						$result->orderby($orderbyTable.'.'.$k,$v);
					}
				}
				$data['result'] = $result->paginate($limit);
			}else{
				$data['result'] = $result->orderby($this->table.'.'.$this->primaryKey,'desc')->paginate($limit);
			}
		}

		$data['columns'] = $columnsTable;

		if($this->indexReturnMode) return $data;

		//LISTING INDEX HTML
		$addAction = $this->data['addaction'];

		if ($this->subModule) {
			foreach ($this->subModule as $s) {
				$tableParent = CB::parseSqlTable($this->table)['table'];
				$addAction[] = [
					'label'=>$s['label'],
					'icon'=>$s['button_icon'],
					'url'=>CB::adminPath($s['path']).'?parentTable='.$tableParent.'&parentColumns='.$s['parentColumns'].'&parentId=[id]&returnURL='.urlencode(Request::fullUrl()).'&foreignKey='.$s['foreignKey'].'&label='.urlencode($s['label']),
					'color'=>$s['button_color'],
					'showIf'=>$s['showIf']
				];
			}
		}

		$mainPath       = CB::mainpath();
		$originMainPath = $this->data['mainpath'];
		$titleField     = $this->titleField;
		$htmlContents   = array();
		$page           = (Request::get('page'))?Request::get('page'):1; 
		$number         = ($page-1)*$limit+1;
		foreach ($data['result'] as $row) {
			$htmlContent = array();

			if ($this->buttonBulkAction) {				
				$htmlContent[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='$row->id'/>";
			}

			if ($this->showNumbering) {
				$htmlContent[] = $number.'. ';
				$number++;
			}

			foreach ($columnsTable as $col) {
				if ($col['visible']===FALSE) continue;

		        $value = @$row->{$col['field']};
		        $title = @$row->{$this->titleField};
		        $label = $col['label'];

		        if (isset($col['image'])) {
			        if ($value=='') {
			            $value = "<a data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='http://placehold.it/50x50&text=NO+IMAGE'><img width='40px' height='40px' src='http://placehold.it/50x50&text=NO+IMAGE'/></a>";
			        } else {
						$pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);
				        $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
			        }			            
		        }

		        if (@$col['download']) {
			        $url = (strpos($value,'http://')!==FALSE)?$value:asset($value).'?download=1';
			        if ($value) {
						$value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
			        } else {
			            $value = " - ";
			        }
		        }

		        if($col['str_limit']) {
		            $value = trim(strip_tags($value));
		            $value = str_limit($value,$col['str_limit']);
		        }

		        if($col['nl2br']) {
					$value = nl2br($value);
		        }

		        //New method for callback
		        if(isset($col['callback'])) {
		        	$value = call_user_func($col['callback'],$row);
		        }

		        $dataValue = @unserialize($value);
					if ($dataValue !== false) {
						if ($dataValue) {
							$preValue = [];
							foreach ($dataValue as $d) {
								if ($d['label']) {
									$preValue[] = $d['label'];
								}
						    }
						    if (count($preValue)) {
						    	$value = implode(", ",$preValue);
						    }
						}
					}

		          $htmlContent[] = $value;
	        } //end foreach columnsTable


			if($this->buttonTableAction):

				$buttonActionStyle = $this->buttonActionStyle;
				$htmlContent[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action',compact('addAction','row','buttonActionStyle','parentField'))->render()."</div>";

			endif;//button_table_action

			$htmlContents[] = $htmlContent;
		} //end foreach data[result]

 		$htmlContents = ['html'=>$htmlContents,'data'=>$data['result']];

		$data['htmlContents'] = $htmlContents;

		return view("crudbooster::default.index",$data);
	}

	public function getExportData()
	{

		return redirect(CB::mainpath());
	}

	public function postExportData()
	{

		$this->limit           = Request::input('limit');
		$this->indexReturnMode = TRUE;
		$fileFormat            = Request::input('fileFormat');
		$fileName              = Request::input('fileName');
		$pageSize              = Request::input('pageSize');
		$pageOrientation       = Request::input('pageOrientation');
		$response              = $this->getIndex();

		if (Request::input('defaultPageSize')) {
			DB::table('cb_settings')->where('name','default_page_size')->update(['content'=>$pageSize]);
		}

		switch($fileType) {
			case "pdf":
				$view = view('crudbooster::export',$response)->render();
				$pdf = App::make('dompdf.wrapper');
				$pdf->loadHTML($view);
				$pdf->setPaper($pageSize,$pageOrientation);
				return $pdf->stream($fileName.'.pdf');
			break;
			case 'xls':
				Excel::create($fileName, function($excel) use ($response) {
					$excel->setTitle($fileName)
					->setCreator("crudbooster.com")
					->setCompany(CB::getSetting('appname'));
				    $excel->sheet($fileName, function($sheet) use ($response) {
				    	$sheet->setOrientation($pageOrientation);
				        $sheet->loadview('crudbooster::export',$response);
				    });
				})->export('xls');
			break;
			case 'csv':
				Excel::create($fileName, function($excel) use ($response) {
					$excel->setTitle($fileName)
					->setCreator("crudbooster.com")
					->setCompany(CB::getSetting('appname'));
				    $excel->sheet($fileName, function($sheet) use ($response) {
				    	$sheet->setOrientation($pageOrientation);
				        $sheet->loadview('crudbooster::export',$response);
				    });
				})->export('csv');
			break;
		}
	}

	public function postDataQuery()
	{
		$query = Request::get('query');
		$query = DB::select(DB::raw($query));
		return response()->json($query);
	}

	public function getDataTable()
	{
		$table = Request::get('table');
		$label = Request::get('label');
		$foreignKeyName = Request::get('fkName');
		$foreignKeyValue = Request::get('fkValue');
		if ($table && $label && $foreignKeyName && $foreignKeyValue) {
			$query = DB::table($table)->select('id as select_value',$label.' as select_label')->where($foreignKeyName,$foreignKeyValue)->orderby($label,'asc')->get();
			return response()->json($query);
		} else {
			return response()->json([]);
		}
	}

	public function getModalData()
	{
		$table = Request::get('table');
		$where = Request::get('where');
		$where = urldecode($where);
		$columns = Request::get('columns');
		$columns = explode(",",$columns);

		$table = CB::parseSqlTable($table)['table'];
		$result = DB::table($table);

		if (Request::get('q')) {
			$result->where(function($where) use ($columns) {
				foreach ($columns as $c=>$col) {
					if ($c==0) {
						$where->where($col,'like','%'.Request::get('q').'%');
					} else {
						$where->orWhere($col,'like','%'.Request::get('q').'%');
					}
				}
			});
		}

		if ($where) {
			$result->whereRaw($where);
		}

		$result->orderBy('id','desc');

		$data['result'] = $result->paginate(6);
		$data['columns'] = $columns;
		return view('crudbooster::default.type_components.datamodal.browser',$data);
	}

	public function getUpdateSingle()
	{
		$table = Request::get('table');
		$column = Request::get('column');
		$value = Request::get('value');
		$id = Request::get('id');

		DB::table($table)->where(CB::pk($table),$id)->update([$column => $value]);

		return redirect()->back()->with(['message_type'=>'success','message'=>trans('crudbooster.alert_delete_data_success')]);
	}

	public function getFindData()
	{
		$q        = Request::get('q');
		$id       = Request::get('id');
		$limit    = Request::get('limit')?:10;
		$format   = Request::get('format');
		
		$table1   = (Request::get('table1'))?:$this->table;
		$column1  = (Request::get('column1'))?:$this->titleField;
		
		@$table2  = Request::get('table2');
		@$column2 = Request::get('column2');
		
		@$table3  = Request::get('table3');
		@$column3 = Request::get('column3');
		
		$where    = Request::get('where');
		
		$fk 	  = Request::get('fk');
		$fkValue = Request::get('fkValue');

		if ($q || $id || $table1) {
			$rows = DB::table($table1);
			$rows->select($table1.'.*');
			$rows->take($limit);

			if (CB::isColumnExists($table1,'deleted_at')) {
				$rows->where($table1.'.deleted_at',NULL);
			}

			if ($fk && $fkValue) {
				$rows->where($table1.'.'.$fk,$fkValue);
			}

			if ($table1 && $column1) {

				$orderbyTable  = $table1;
				$orderbyColumn = $column1;
			}

			if ($table2 && $column2) {
				$rows->join($table2,$table2.'.id','=',$table1.'.'.$column1);
				$columns = CB::getTableColumns($table2);
				foreach ($columns as $col) {
					$rows->addselect($table2.".".$col." as ".$table2."_".$col);
				}
				$orderbyTable  = $table2;
				$orderbyColumn = $column2;
			}

			if ($table3 && $column3) {
				$rows->join($table3,$table3.'.id','=',$table2.'.'.$column2);
				$columns = CB::getTableColumns($table3);
				foreach ($columns as $col) {
					$rows->addselect($table3.".".$col." as ".$table3."_".$col);
				}
				$orderbyTable  = $table3;
				$orderbyColumn = $column3;
			}

			if ($id) {				
				$rows->where($table1.".".CB::pk($table1),$id);
			}

			if ($where) {
				$rows->whereRaw($where);
			}

			if ($format) {
				$format = str_replace('&#039;', "'", $format);
				$rows->addselect(DB::raw("CONCAT($format) as text"));
				if ($q) $rows->whereraw("CONCAT($format) like '%".$q."%'");
			} else {
				$rows->addselect($orderbyTable.'.'.$orderbyColumn.' as text');
				if($q) $rows->where($orderbyTable.'.'.$orderbyColumn,'like','%'.$q.'%');
			}

			$result          = array();
			$result['items'] = $rows->get();
		} else {
			$result          = array();
			$result['items'] = array();
		}
		return response()->json($result);
	}

	public function validation($id=NULL)
	{
		$requestAll = Request::all();
		$validationRules = array();
		foreach($this->inputs as $di) {
			$ai = array();
			$name = $di['name'];

			if ( !isset($requestAll[$name]) ) continue;

			if ($di['type'] != 'upload') {
				if(@$di['required']) {
					$ai[] = 'required';
				}
			}

			if ($di['type'] == 'upload') {
				if ($id) {
					$row = DB::table($this->table)->where($this->primaryKey,$id)->first();
					if ($row->{$di['name']}=='') {
						$ai[] = 'required';
					}
				}
			}

			if (@$di['min']) {
				$ai[] = 'min:'.$di['min'];
			}
			
			if (@$di['max']) {
				$ai[] = 'max:'.$di['max'];
			}
			
			if (@$di['image']) {
				$ai[] = 'image';
			}
			
			if (@$di['mimes']) {
				$ai[] = 'mimes:'.$di['mimes'];
			}
			
			$name = $di['name'];
			if (!$name) continue;

			if ($di['type']=='money') {
				$requestAll[$name] = preg_replace('/[^\d-]+/', '', $requestAll[$name]);
			}


			if (@$di['validation']) {
				$validationArray = explode('|',$di['validation']);
				if (count($validationArray)) {
					foreach ($validationArray as &$validationItem) {
						if (substr($validationItem, 0,6) == 'unique') {
							$parseUnique = explode(',',str_replace('unique:','',$validationItem));
							$uniqueTable = ($parseUnique[0])?:$this->table;
							$uniqueColumn = ($parseUnique[1])?:$name;
							$uniqueIgnoreId = ($parseUnique[2])?:(($id)?:'');

							//Make sure table name
							$uniqueTable = CB::parseSqlTable($uniqueTable)['table'];

							//Rebuild unique rule
							$uniqueRebuild = [];
							$uniqueRebuild[] = $uniqueTable;
							$uniqueRebuild[] = $uniqueColumn;
							if ($uniqueIgnoreId) {							
								$uniqueRebuild[] = $uniqueIgnoreId;
							}

							//Check whether deleted_at exists or not
							if (CB::isColumnExists($uniqueTable,'deleted_at')) {
								$uniqueRebuild[] = CB::findPrimaryKey($uniqueTable);
								$uniqueRebuild[] = 'deleted_at';
								$uniqueRebuild[] = 'NULL';							
							}							
							$uniqueRebuild = array_filter($uniqueRebuild);
							$validationItem = 'unique:'.implode(',',$uniqueRebuild);
						}
					}
				} else {
					$validationArray = array();
				}
								
				$validationRules[$name] = implode('|',$validationArray);
			} else {
				$validationRules[$name] = implode('|',$ai);
			}
		}				

		$validator = Validator::make($requestAll,$validationRules);

		if ($validator->fails()) {
			$message = $validator->messages();
			$message_all = $message->all();

			if (Request::ajax()) {
				$res = response()->json(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->send();
				exit;
			} else {
				$res = redirect()->back()->with("errors",$message)->with(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->withInput();
				\Session::driver()->save();
				$res->send();
	        	exit;
			}

		}
	}

	public function inputAssignment($id=null)
	{
		$hiddenInputs = (Request::get('hiddenInputs'))?unserialize(Request::get('hiddenInputs')):[];
		$passwordCandidates = explode(',',config('crudbooster.password_field_candidate'));

		foreach($this->inputs as $input) {			

			if (!$input['name']) continue;			

			if ($input['exception']) continue;

			if ($input['name']=='hiddenInputs') continue;

			if (count($hiddenInputs)) {
				if (in_array($input['name'], $hiddenInputs)) {
					continue;
				}
			}

			if ($input['type']=='checkbox' && $input['relationshipTable']) {
				continue;
			}

			if ($input['type']=='select2' && $input['relationshipTable']) {
				continue; 
			}

			$inputName = $input['name'];
			$inputValue = Request::get($inputName);

			if ($input['type']=='money') {
				$inputValue = preg_replace('/[^\d-]+/', '', $inputValue);
			}

			if ($input['type']=='child') continue;

			if ($inputName) {
				if($inputValue!='') {
					$this->inputAssignmentData[$inputName] = $inputValue;
				}else{
					$this->inputAssignmentData[$inputName] = "";
				}
			}
			
			if (in_array($inputName, $passwordCandidates)) {
				if(!empty($this->inputAssignmentData[$inputName])) {
					$this->inputAssignmentData[$inputName] = Hash::make($this->inputAssignmentData[$inputName]);
				}else{
					unset($this->inputAssignmentData[$inputName]);
				}
			}

			if ($input['type']=='checkbox') {

				if (is_array($inputValue)) {
					if ($input['dataTable'] != '') {						
						$checkboxTable = explode(',',$input['dataTable'])[0];
						$checkboxField = explode(',',$input['dataTable'])[1];
						$checkboxPK = CB::findPrimaryKey($checkboxTable);
						$checkboxRow = DB::table($checkboxTable)->whereIn($checkboxPK,$inputValue)->pluck($checkboxField)->toArray();
						$this->inputAssignmentData[$inputName] = implode(";",$checkboxRow);
					} else {
						$this->inputAssignmentData[$inputName] = implode(";",$inputValue);
					}
				}
			}

			if ($input['type']=='googlemaps') {
				if ($input['latitude'] && $input['longitude']) {
					$latitudeName = $input['latitude'];
					$longitudeName = $input['longitude'];
					$this->inputAssignmentData[$latitudeName] = Request::get('input-latitude-'.$inputName);
					$this->inputAssignmentData[$longitudeName] = Request::get('input-longitude-'.$inputName);
				}
			}

			if ($input['type']=='select' || $input['type']=='select2') {
				if ($input['dataTable']) {
					if ($inputValue=='') {
						$this->inputAssignmentData[$inputName] = 0;
					}
				}
			}

			if (@$input['type']=='upload') {

				if (Request::hasFile($inputName)) {
					$file = Request::file($inputName);
					$ext  = $file->getClientOriginalExtension();
					$fileName = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

					//Create Directory Monthly
					Storage::makeDirectory(date('Y-m'));

					//Move file to storage								
					$filePath = storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m'));

					if ($input['upload_encrypt'] && $input['upload_encrypt']==true) {
						$filename = md5(str_random(5)).'.'.$ext;
					} else {
						if (count(glob($file_path.'/'.$filename))>0) {
							$fileName = $fileName.'_'.count(glob($filePath."/$fileName*.$ext")).'.'.$ext;

						} else {
							$fileName = $fileName.'.'.$ext;
						}
					}

					if ($file->move($filePath,$fileName)) {
						$this->inputAssignmentData[$inputName] = 'uploads/'.date('Y-m').'/'.$fileName;
					}
				}

				if (!$this->inputAssignmentData[$inputName]) {
					$this->inputAssignmentData[$inputName] = Request::get('_'.$inputName);
				}
			}

			if (@$input['type']=='filemanager') {
				$url = str_replace(asset('/'),'',$this->inputAssignmentData[$inputName]);
				$url = str_replace("//","/",$url);
				$this->inputAssignmentData[$inputName] = $url;
			}
		}
	}

	public function getAdd()
	{		
		if (!CB::canCreate() && $this->globalRoles==FALSE || $this->buttonAdd==FALSE) {
			CB::insertLog(trans('crudbooster.log_try_add',['module'=>CB::getCurrentModule()->name ]));
			CB::redirect(CB::adminPath(),trans("crudbooster.denied_access"));
		}

		$pageTitle      = trans("crudbooster.add_data_page_title",['module'=>CB::getCurrentModule()->name]);		
		$command 		 = 'add';

		return view('crudbooster::default.form',compact('pageTitle','command'));
	}

	public function postAddSave()
	{		
		if (!CB::canCreate() && $this->globalRoles==FALSE) {
			CB::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->titleField),'module'=>CB::getCurrentModule()->name ]));
			CB::redirect(CB::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation();
		$this->inputAssignment();		

		if(Schema::hasColumn($this->table, 'created_at')) {
		    $this->inputAssignmentData['created_at'] = date('Y-m-d H:i:s');
		}

		$this->hookBeforeAdd($this->inputAssignmentData);

		$this->inputAssignmentData[$this->primaryKey] = $id = CB::newId($this->table);		
		DB::table($this->table)->insert($this->inputAssignmentData);		

		//Looping Data Input Again After Insert
		foreach($this->inputs as $input) {
			$name = $input['name'];
			if(!$name) continue;

			$inputData = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if ($input['type'] == 'checkbox') {
				if ($input['relationshipTable']) {
					$dataTable = explode(",",$input['dataTable'])[0];
					$foreignKey2 = CB::getForeignKey($dataTable,$input['relationshipTable']);
					$foreignKey = CB::getForeignKey($this->table,$input['relationshipTable']);
					DB::table($input['relationshipTable'])->where($foreignKey,$id)->delete();

					if ($inputData) {
						foreach($inputData as $inputId) {
							DB::table($input['relationshipTable'])->insert([
								'id'=>CB::newId($input['relationshipTable']),
								$foreignKey=>$id,
								$foreignKey2=>$inputId
								]);
						}
					}
				}
			}


			if ($input['type'] == 'select2') {
				if ($input['relationshipTable']) {
					$dataTable = explode(",",$input['dataTable'])[0];
					$foreignKey2 = CB::getForeignKey($dataTable,$input['relationshipTable']);
					$foreignKey = CB::getForeignKey($this->table,$input['relationshipTable']);
					DB::table($input['relationshipTable'])->where($foreignKey,$id)->delete();

					if ($inputData) {
						foreach($inputData as $inputId) {
							DB::table($input['relationshipTable'])->insert([
								'id'=>CB::newId($input['relationshipTable']),
								$foreignKey=>$id,
								$foreignKey2=>$inputId
								]);
						}
					}
				}
			}

			if ($input['type']=='child') {
				$name = str_slug($input['label'],'');
				$columns = $input['columns'];				
				$countInputData = count(Request::get($name.'-'.$columns[0]['name']))-1;
				$childArray = [];

				for($i=0;$i<=$countInputData;$i++) {
					$fk = $input['foreignKey'];
					$column = [];
					$column[$fk] = $id;
					foreach($columns as $col) {
						$colName = $col['name'];
						$column[$colName] = Request::get($name.'-'.$colName)[$i];
					}
					$childArray[] = $column;
				}	

				$childTable = CB::parseSqlTable($input['table'])['table'];
				DB::table($childTable)->insert($childArray);
			}
		}

		$this->hookAfterEdit($this->inputAssignmentData[$this->primaryKey]);

		$this->returnURL = ($this->returnURL)?$this->returnURL:Request::get('returnURL');

		//insert log
		CB::insertLog(trans("crudbooster.log_add",['name'=>$this->inputAssignmentData[$this->titleField],'module'=>CB::getCurrentModule()->name]));

		if($this->returnURL) {
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CB::redirect(Request::server('HTTP_REFERER'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CB::redirect($this->returnURL,trans("crudbooster.alert_add_data_success"),'success');
			}

		}else{
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CB::redirect(CB::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CB::redirect(CB::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
			}
		}
	}

	public function getEdit($id)
	{				
		$row = CB::first($this->table,$id);

		if (!CB::canRead() && $this->globalRoles==FALSE || $this->buttonEdit==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_edit",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}		
		$pageTitle 	 = trans("crudbooster.edit_data_page_title",['module'=>CB::getCurrentModule()->name,'name'=>$row->{$this->titleField}]);
		$command 		 = 'edit';
		Session::put('current_row_id',$id);
		return view('crudbooster::default.form',compact('id','row','page_menu','pageTitle','command'));
	}

	public function postEditSave($id)
	{				
		$row = CB::first($this->table,$id);

		if (!CB::canEdit() && $this->globalRoles==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$this->validation($id);
		$this->inputAssignment($id);

		if (Schema::hasColumn($this->table, 'updated_at')) {
		    $this->inputAssignmentData['updated_at'] = date('Y-m-d H:i:s');
		}

		$this->hookBeforeEdit($this->inputAssignmentData,$id);		
		DB::table($this->table)->where($this->primaryKey,$id)->update($this->inputAssignmentData);

		//Looping Data Input Again After Insert
		foreach ($this->inputs as $input) {
			$name = $input['name'];
			if(!$name) continue;

			$inputData = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if ($input['type'] == 'checkbox') {
				if($input['relationship_table']) {
					$dataTable = explode(",",$input['datatable'])[0];					

					$foreignKey2 = CB::getForeignKey($dataTable,$input['relationship_table']);
					$foreignKey = CB::getForeignKey($this->table,$input['relationship_table']);
					DB::table($input['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputData) {
						foreach($inputData as $inputId) {
							DB::table($input['relationship_table'])->insert([
								'id'=>CB::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
					

				}
			}

			if ($input['type'] == 'select2') {
				if($input['relationship_table']) {
					$dataTable = explode(",",$input['datatable'])[0];					
					
					$foreignKey2 = CB::getForeignKey($dataTable,$input['relationship_table']);
					$foreignKey = CB::getForeignKey($this->table,$input['relationship_table']);
					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputData) {
						foreach($inputData as $inputId) {
							DB::table($input['relationship_table'])->insert([
								'id'=>CB::newId($input['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
					

				}
			}

			if ($input['type']=='child') {
				$name = str_slug($input['label'],'');
				$columns = $ro['columns'];				
				$countInputData = count(Request::get($name.'-'.$columns[0]['name']))-1;
				$childArray = [];
				$childTable = CB::parseSqlTable($input['table'])['table'];
				$fk = $input['foreign_key'];

				DB::table($childTable)->where($fk,$id)->delete();
				$lastId = CB::newId($childTable);

				for($i=0;$i<=$countInputData;$i++) {
					
					$columnData = [];
					$columnData['id'] = $lastId;
					$columnData[$fk] = $id;
					foreach($columns as $col) {
						$colName = $col['name'];
						$columnData[$colName] = Request::get($name.'-'.$colName)[$i];
					}
					$child_array[] = $column_data;

					$lastId++;
				}	

				$childArray = array_reverse($childArray);
				
				DB::table($childTable)->insert($childArray);
			}


		}

		$this->hookAfterEdit($id);


		$this->returnURL = ($this->returnURL)?$this->returnURL:Request::get('returnURL');

		//insert log
		CB::insertLog(trans("crudbooster.log_update",['name'=>$this->arr[$this->titleField],'module'=>CB::getCurrentModule()->name]));

		if ($this->returnURL) {
			CB::redirect($this->returnURL,trans("crudbooster.alert_update_data_success"),'success');
		} else {
			if (Request::get('submit') == trans('crudbooster.button_save_more')) {
				CB::redirect(CB::mainpath('add'),trans("crudbooster.alert_update_data_success"),'success');
			} else {
				CB::redirect(CB::mainpath(),trans("crudbooster.alert_update_data_success"),'success');
			}
		}
	}

	public function getDelete($id)
	{				
		$row = CB::first($this->table,$id);

		if (!CB::canDelete() && $this->globalRoles==FALSE || $this->buttonDelete==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		//insert log
		CB::insertLog(trans("crudbooster.log_delete",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));

		$this->hookBeforeDelete($id);

		if (CB::isColumnExists($this->table,'deleted_at')) {
			DB::table($this->table)->where($this->primaryKey,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
		} else {
			DB::table($this->table)->where($this->primaryKey,$id)->delete();
		}


		$this->hookAfterDelete($id);

		$url = g('return_url')?:CB::referer();

		CB::redirect($url,trans("crudbooster.alert_delete_data_success"),'success');
	}

	public function getDetail($id)
	{				
		$row = CB::first($this->table,$id);

		if (!CB::canRead() && $this->globalRoles==FALSE || $this->buttonDetail==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$module     = CB::getCurrentModule();

		$page_menu  = Route::getCurrentRoute()->getActionName();
		$page_title = trans("crudbooster.detail_data_page_title",['module'=>CB::getCurrentModule()->name,'name'=>$row->{$this->title_field}]);
		$command    = 'detail';

		Session::put('current_row_id',$id);

		return view('crudbooster::default.form',compact('row','page_menu','pageTitle','command','id'));
	}

	public function getImportData()
	{
		$data['pageTitle']      = 'Import Data';

		if (Request::get('file') && !Request::get('import')) {
			$file = base64_decode(Request::get('file'));
			$file = trim(str_replace('uploads','app',$file),'/');
			$file = storage_path($file);
			$rows = Excel::load($file,function($reader) {
			})->get();

			Session::put('total_data_import',count($rows));

			$dataImportColumn = array();
			foreach($rows as $value) {
				$a = array();
				foreach($value as $k=>$v) {
					$a[] = $k;
				}
				if(count($a)) {
					$dataImportColumn = $a;
				}
				break;
			}

			$tableColumns = CB::getTableColumns($this->table);

			$data['tableColumns'] = $tableColumns;
			$data['dataImportColumn'] = $dataImportColumn;
		}


		return view('crudbooster::import',$data);
	}

	public function postDoneImport()
	{				
		$data['pageTitle']      = trans('crudbooster.import_page_title',['module'=>'']);
		Session::put('select_column',Request::get('select_column'));
		return view('crudbooster::import',$data);
	}

	public function postDoImportChunk()
	{		
		$fileMD5 = md5(Request::get('file'));

		if (Request::get('file') && Request::get('resume')==1) {
			$total = Session::get('total_data_import');
			$prog = intval(Cache::get('success_'.$fileMD5)) / $total * 100;
			$prog = round($prog,2);
			if($prog >= 100) {
				Cache::forget('success_'.$fileMD5);
			}
			return response()->json(['progress'=> $prog, 'last_error'=>Cache::get('error_'.$fileMD5) ]);
		}

		$selectColumn = Session::get('select_column');
		$selectColumn = array_filter($selectColumn);
		$tableColumns = CB::getTableColumns($this->table);


		$file = base64_decode(Request::get('file'));
		$file = trim(str_replace('uploads','app',$file),'/');
		$file = storage_path($file);

		$rows = Excel::load($file,function($reader) {
		})->get();

		$hasCreatedAt = false;
		if (CB::isColumnExists($this->table,'created_at')) {
			$hasCreatedAt = true;
		}

		$dataImportColumn = array();

		foreach ($rows as $value) {
			$a = array();
			foreach ($selectColumn as $sk => $s) {
				$colName = $tableColumns[$sk];

				if (CB::isForeignKey($colName)) {

					//Skip if value is empty
					if ($value->$s == '') continue;

					if (intval($value->$s)) {
						$a[$colName] = $value->$s;
					} else {
						$relationTable = CB::getTableForeignKey($colName);
						$relationModules = DB::table('cms_moduls')->where('table_name',$relationTable)->first();

						$relationClass = __NAMESPACE__ . '\\' . $relationModules->controller;
						if(!class_exists($relationClass)) {
							$relationClass = '\App\Http\Controllers\\'.$relationModules->controller;
						}
						$relationClass = new $relationClass;						

						$titleField = $relationClass->titleField;

						$relationInsertData = array();
						$relationInsertData[$titleField] = $value->$s;

						if(CB::isColumnExists($relationTable,'created_at')) {
							$relationInsertData['created_at'] = date('Y-m-d H:i:s');
						}

						try {
							$relationExists = DB::table($relationTable)->where($titleField,$value->$s)->first();
							if ($relationExists) {
								$relationPrimaryKey = $relationClass->primaryKey;
								$relationId = $relation_exists->$relationPrimaryKey;
							} else {
								$relationId = DB::table($relationTable)->insertGetId($relationInsertData);
							}

							$a[$colName] = $relationId;
						} catch (\Exception $e) {
							exit($e);
						}
					} //END IS INT

				} else {
					$a[$colName] = $value->$s;
				}
			}

			$hasTitle = true;
			foreach ($a as $k=>$v) {
				if ($k == $this->titleField && $v == '') {
					$hasTitle = false;
					break;
				}
			}

			if($hasTitle==false) continue;

			try {

				if ($hasCreatedAt) {
					$a['created_at'] = date('Y-m-d H:i:s');
				}

				DB::table($this->table)->insert($a);
				Cache::increment('success_'.$fileMD5);
			} catch (\Exception $e) {
				$e = (string) $e;
				Cache::put('error_'.$fileMD5,$e,500);
			}
		}
		return response()->json(['status'=>true]);
	}

	public function postDoUploadImportData()
	{		
		if (Request::hasFile('userfile')) {

			$file = Request::file('userfile');
			$ext  = $file->getClientOriginalExtension();

			$validator = Validator::make([
											'extension'=>$ext,
										],[
											'extension'=>'in:xls,xlsx,csv'
										]);

		    if ($validator->fails()) {
		        $message = $validator->errors()->all();
		        return redirect()->back()->with(['message'=>implode('<br/>',$message),'message_type'=>'warning']);
		    }

			//Create Directory Monthly
			Storage::makeDirectory(date('Y-m'));

			//Move file to storage
			$fileName = md5(str_random(5)).'.'.$ext;
			$fileNameURL = '';
			if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$fileName)) {
				$fileNameURL = 'uploads/'.date('Y-m').'/'.$fileName;
			}
			$url = CB::mainpath('import-data').'?file='.base64_encode($fileNameURL);
			return redirect($url);
		}else{
			return redirect()->back();
		}
	}

	public function postActionSelected()
	{		
		$selectedId = Request::input('checkbox');
		$buttonName = Request::input('button_name');

		if ($buttonName == 'delete') {
			if(!CB::canDelete()) {
				CB::insertLog(trans("crudbooster.log_try_delete_selected",['module'=>CB::getCurrentModule()->name]));
				CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
			}

			$this->hookBeforeDelete($selectedId);
			$
			if(CB::isColumnExists($this->table,'deleted_at')) {
				DB::table($this->table)->whereIn($this->primaryKey,$selectedId)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->whereIn($this->primaryKey,$selectedId)->delete();
			}
			CB::insertLog(trans("crudbooster.log_delete",['name'=>implode(',',$selectedId),'module'=>CB::getCurrentModule()->name]));

			$this->hookAfterDelete($selectedId);

			$message = trans("crudbooster.alert_delete_selected_success");
			return redirect()->back()->with(['message_type'=>'success','message'=>$message]);
		}

		$this->actionButtonSelected($selectedId,$buttonName);

		$action = str_replace(['-','_'],' ',$buttonName);
		$action = ucwords($action);
		$message = trans("crudbooster.alert_action",['action'=>$action]);
		return redirect()->back()->with(['message_type'=>'success','message'=>$message]);
	}

	public function getDeleteImage()
	{		
		$id     = Request::get('id');
		$column = Request::get('column');		
		$row = CB::first($this->table,$id);

		if (!CB::canDelete() && $this->globalRoles==FALSE) {
			CB::insertLog(trans("crudbooster.log_try_delete_image",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}		

		$file = str_replace('uploads/','',$row->{$column});
		if (Storage::exists($file)) {
        	Storage::delete($file);
       	}

       	DB::table($this->table)->where($this->primaryKey,$id)->update([$column=>NULL]);

		CB::insertLog(trans("crudbooster.log_delete_image",['name'=>$row->{$this->titleField},'module'=>CB::getCurrentModule()->name]));

		CB::redirect(Request::server('HTTP_REFERER'),trans('crudbooster.alert_delete_data_success'),'success');
	}

	public function postUploadSummernote()
	{		
		$name = 'userfile';
		if (Request::hasFile($name)) {
			$file = Request::file($name);
			$ext  = $file->getClientOriginalExtension();

			//Create Directory Monthly
			Storage::makeDirectory(date('Y-m'));

			//Move file to storage
			$fileName = md5(str_random(5)).'.'.$ext;
			if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$fileName)) {
				echo asset('uploads/'.date('Y-m').'/'.$fileName);
			}
		}
	}

	public function postUploadFile()
	{		
		$name = 'userfile';
		if (Request::hasFile($name)) {
			$file = Request::file($name);
			$ext  = $file->getClientOriginalExtension();

			//Create Directory Monthly
			Storage::makeDirectory(date('Y-m'));

			//Move file to storage
			$fileName = md5(str_random(5)).'.'.$ext;
			if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$fileName)) {
				echo 'uploads/'.date('Y-m').'/'.$fileName;
			}
		}
	}

	public function actionButtonSelected($selectedId,$buttonName)
	{
    }

	public function hookQueryIndex(&$query)
	{
	}

	public function hookBeforeAdd(&$arr)
	{
	}

	public function hookAfterAdd($id)
	{
	}

	public function hookBeforeEdit(&$arr,$id)
	{
	}

	public function hookAfterEdit($id)
	{
	}

	public function hookBeforeDelete($id)
	{
	}

	public function hookAfterDelete($id)
	{
	}

}
