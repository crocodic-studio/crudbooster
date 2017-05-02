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

class CBStatisticBuilderController extends CBController
{

	public function cbInit()
	{
		$this->table             = 'cms_statistics';
		$this->primaryKey        = 'id';
		$this->titleField        = 'name';
		$this->limit             = 20;
		$this->orderBy           = 'id,desc';
		$this->globalRoles       = FALSE;
		
		$this->buttonTableAction = TRUE;
		$this->buttonActionStyle = "button_icon_text";
		$this->buttonAdd         = TRUE;
		$this->buttonDelete      = TRUE;
		$this->buttonEdit        = TRUE;
		$this->buttonDetail      = FALSE;
		$this->buttonShow        = TRUE;
		$this->buttonFilter      = FALSE;
		$this->buttonExport      = FALSE;
		$this->buttonImport      = FALSE;

		$this->columns         = array();
		$this->columns[]       = array("label"=>"Name","name"=>"name" );
		
		$this->inputs        = array();
		$this->inputs[]      = array("label"=>"Name","name"=>"name","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255","placeholder"=>"You can only enter the letter only");
		
		$this->addAction   = array();
		$this->addAction[] = ['label'=>'Builder','url'=>CRUDBooster::mainpath('builder').'/[id]','icon'=>'fa fa-wrench'];
	}

	public function getShowDashboard()
	{		
		$m = CRUDBooster::sidebarDashboard();
		$m->path = str_replace("statistic_builder/show/","",$m->path);
		if ($m->type != 'Statistic') {
			return redirect('/');
		}		
		$row = CRUDBooster::first($this->table,['permalink'=>$m->path]);
		$cb_statistics_id = $row->id;
		$page_title        = $row->name;
		return view('crudbooster::statistic_builder.show',compact('page_title','cb_statistics_id'));
	}

	public function getShow($permalink)
	{		
		$row               = CRUDBooster::first($this->table,['permalink'=>$permalink]);
		$cb_statistics_id  = $row->id;
		$page_title        = $row->name;
		return view('crudbooster::statistic_builder.show',compact('page_title','cb_statistics_id'));
	}

	public function getBuilder($cb_statistics_id)
	{		
		if (!CB::isSuperadmin()) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>'Builder','module'=>'Statistic']));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$page_title = 'Statistic Builder';
		return view('crudbooster::statistic_builder.builder',compact('page_title','cb_statistics_id'));
	}

	public function getListComponent($cb_statistics_id,$areaName)
	{
		$rows = DB::table('cb_statistic_components')
		->where('cb_statistics_id',$cb_statistics_id)
		->where('area_name',$areaName)
		->orderby('sorting','asc')->get();

		return response()->json(['components'=>$rows]);
	}
	
	public function getViewComponent($componentID)
	{
		$component = CB::first('cb_statistic_components',['component_id'=>$componentID]);	

		$command = 'layout';
		$layout = view('crudbooster::statistic_builder.components.'.$component->component_name,compact('command','componentID'))->render();

		$componentName = $component->component_name;
		$areaName = $component->area_name;
		$config = json_decode($component->config);
		if ($config) {
			foreach ($config as $key=>$value) {
				if ($value) {
					$command = 'showFunction';
					$value = view('crudbooster::statistic_builder.components.'.$componentName,compact('command','value','key','config','componentID'))->render();
					$layout = str_replace('['.$key.']',$value,$layout);
				}
			}
		}

		return response()->json(compact('componentID','layout'));
	}

	public function postAddComponent()
	{		
		$componentName    = Request::get('component_name');
		$cb_statistics_id = Request::get('cb_statistics_id');
		$sorting           = Request::get('sorting');
		$area 			   = Request::get('area');

		$componentID = md5(time());

		$command = 'layout';
		$layout = view('crudbooster::statistic_builder.components.'.$componentName,compact('command','componentID'))->render();

		$data = [
			'cb_statistics_id'=>$cb_statistics_id,
			'component_id'=>$componentID,
			'component_name'=>$componentName,
			'area_name'=>$area,
			'sorting'=>$sorting,
			'name'=>'Untitled'
		];

		CRUDBooster::insert('cms_statistic_components',$data);

		return response()->json(compact('layout','componentID'));
	}

	public function postUpdateAreaComponent()
	{
		DB::table('cb_statistic_components')->where('componentID',Request::get('componentID'))
												->update([
													'sorting'=>Request::get('sorting'),
													'area_name'=>Request::get('areaname')	    		
													]);

		return response()->json(['status'=>true]);
	}

	public function getEditComponent($componentID)
	{		
		if (!CB::isSuperadmin()) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>'Edit Component','module'=>'Statistic']));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		$row = CB::first('cms_statistic_components',['componentID'=>$componentID]);

		$config = json_decode($row->config);

		$command = 'configuration';
		return view('crudbooster::statistic_builder.components.'.$row->component_name,compact('command','componentID','config'));
	}

	public function postSaveComponent()
	{
		DB::table('cb_statistic_components')
		->where('componentID',Request::get('componentID'))
		->update(['name'=>Request::get('name'),'config'=>json_encode(Request::get('config'))]);

		return response()->json(['status'=>true]);
	}

	public function getDeleteComponent($componentID)
	{
		if (!CB::isSuperadmin()) {
			CB::insertLog(trans("crudbooster.log_try_view",['name'=>'Delete Component','module'=>'Statistic']));
			CB::redirect(CB::adminPath(),trans('crudbooster.denied_access'));
		}

		DB::table('cb_statistic_components')->where('componentID',$componentID)->delete();
		return response()->json(['status'=>true]);
	}

	public function hookBeforeAdd(&$postData)
	{		
		$postData['slug'] = str_slug($postData['name']);
	}

}
