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
	use CRUDBooster;

	class StatisticBuilderController extends CBController {

	    public function __construct() {
	        $this->table              = "cms_statistics";
	        $this->primary_key        = "id";
	        $this->title_field        = "name";
	        $this->limit              = 20;
	        $this->orderby      	  = ["id"=>"desc"];
	        $this->global_privilege   = FALSE;
	        
			$this->button_table_action = TRUE;   
			$this->button_action_style = "button_icon_text";     
			$this->button_add          = TRUE;
			$this->button_delete       = TRUE;
			$this->button_edit         = TRUE;
			$this->button_detail       = FALSE;
			$this->button_show         = TRUE;
			$this->button_filter       = FALSE;        
			$this->button_export       = FALSE;	        
			$this->button_import       = FALSE;							      

			$this->col         = array();
			$this->col[]       = array("label"=>"Name","name"=>"name" );
			$this->col[]       = array("label"=>"Slug","name"=>"slug",'callback_php'=>'"<span class=\"badge badge-default\">statistic_builder/show/".$row->slug."</span>"');
			
			$this->form        = array();
			$this->form[]      = array("label"=>"Name","name"=>"name","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255","placeholder"=>"You can only enter the letter only");
			
			$this->addaction   = array();
			$this->addaction[] = ['label'=>'Builder','url'=>CRUDBooster::mainpath('builder').'/[id]','icon'=>'fa fa-wrench'];

	        //No need chanage this constructor
	        $this->constructor();
	    }	    

	    public function getShow($slug) {
			$row               = CRUDBooster::first($this->table,['slug'=>$slug]);
			$id_cms_statistics = $row->id;
			$page_title        = $row->name;	    				
	    	return view('crudbooster::statistic_builder.show',compact('page_title','id_cms_statistics'));
	    }

	    public function getBuilder($id_cms_statistics) {
	    	$page_title = 'Statistic Builder';	    		    	
	    	return view('crudbooster::statistic_builder.builder',compact('page_title','id_cms_statistics'));
	    }

	    public function getListComponent($id_cms_statistics,$area_name) {
	    	$rows = DB::table('cms_statistic_components')
	    	->where('id_cms_statistics',$id_cms_statistics)
	    	->where('area_name',$area_name)
	    	->orderby('sorting','asc')->get();

	    	return response()->json(['components'=>$rows]);
	    }
	    public function getViewComponent($componentID) {
	    	$component = CRUDBooster::first('cms_statistic_components',['componentID'=>$componentID]);	

	    	$command = 'layout';	    	
	    	$layout = view('crudbooster::statistic_builder.components.'.$component->component_name,compact('command','componentID'))->render();

	    	$component_name = $component->component_name;
	    	$area_name = $component->area_name;
	    	$config = json_decode($component->config);
	    	if($config) {
	    		foreach($config as $key=>$value) {
	    			if($value) {
    					$command = 'showFunction';
    					$value = view('crudbooster::statistic_builder.components.'.$component_name,compact('command','value','key','config','componentID'))->render();
	    				$layout = str_replace('['.$key.']',$value,$layout);
    				}	    			
	    		}
	    	}	    	

	    	return response()->json(compact('componentID','layout'));
	    }

	    public function postAddComponent() {

			$component_name    = Request::get('component_name');
			$id_cms_statistics = Request::get('id_cms_statistics');
			$sorting           = Request::get('sorting');
			$area 			   = Request::get('area');

	    	$componentID = md5(time());

	    	$command = 'layout';
	    	$layout = view('crudbooster::statistic_builder.components.'.$component_name,compact('command','componentID'))->render();
	    	
	    	$data = [
	    		'id_cms_statistics'=>$id_cms_statistics,
	    		'componentID'=>$componentID,
	    		'component_name'=>$component_name,
	    		'area_name'=>$area,
	    		'sorting'=>$sorting,
	    		'name'=>'Untitled'
	    	];
	    	CRUDBooster::insert('cms_statistic_components',$data);

	    	return response()->json(compact('layout','componentID'));
	    }

	    public function postUpdateAreaComponent() {
	    	DB::table('cms_statistic_components')
	    	->where('componentID',Request::get('componentid'))
	    	->update([
	    		'sorting'=>Request::get('sorting'),
	    		'area_name'=>Request::get('areaname')	    		
	    		]);
	    	return response()->json(['status'=>true]);
	    }

	    public function getEditComponent($componentID) {

	    	$component_row = CRUDBooster::first('cms_statistic_components',['componentID'=>$componentID]);

	    	$config = json_decode($component_row->config);

	    	$command = 'configuration';
	    	return view('crudbooster::statistic_builder.components.'.$component_row->component_name,compact('command','componentID','config'));	
	    }

	    public function postSaveComponent() {
	    	DB::table('cms_statistic_components')
	    	->where('componentID',Request::get('componentid'))
	    	->update([
	    		'name'=>Request::get('name'),
	    		'config'=>json_encode(Request::get('config'))
	    		]);
	    	return response()->json(['status'=>true]);
	    }

	    public function getDeleteComponent($id) {
	    	DB::table('cms_statistic_components')->where('componentID',$id)->delete();
	    	return response()->json(['status'=>true]);
	    }

	   
	    public function hook_before_add(&$arr) {        
	        //Your code here
	    	$arr['slug'] = str_slug($arr['name']);
	    }


	}