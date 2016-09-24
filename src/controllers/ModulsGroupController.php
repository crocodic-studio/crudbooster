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

class ModulsGroupController extends CBController {

		

	public function __construct() {		
		$this->table         = 'cms_moduls_group';
		$this->primary_key   = 'id';
		$this->title_field   = "nama_group";
		$this->index_orderby = array("sorting_group"=>"asc");			

		$this->alert[] = ['message'=>"<p>Please make sure you have created a table before create a module</p>",'type'=>'warning']; 

		$this->col = array();				
		$this->col[] = array("label"=>"No.","name"=>"sorting_group",'width'=>'5%');
		$this->col[] = array("label"=>"Name","name"=>"nama_group");
		$this->col[] = array("label"=>"Type",'width'=>'5%',"name"=>"is_group","callback_php"=>'($row->is_group)?"<span class=\"label label-primary\">GROUP</span>":"<span class=\"label label-default\">NON GROUP</span>"');

		$this->form = array();		
		$this->form[] = array("label"=>"Name","name"=>"nama_group");
		$this->form[] = array("label"=>"Icon","name"=>"icon_group","type"=>"radio","dataenum"=>array(
				"fa fa-bars|<i class='fa fa-bars'></i>",
				"fa fa-cog|<i class='fa fa-cog'></i>",
				"fa fa-comment|<i class='fa fa-comment'></i>",
				"fa fa-users|<i class='fa fa-users'></i>",
				"fa fa-file|<i class='fa fa-file'></i>",
				"fa fa-database|<i class='fa fa-database'></i>",
				"fa fa-bank|<i class='fa fa-bank'></i>",				
				"fa fa-check-square|<i class='fa fa-check-square'></i>",
				"fa fa-car|<i class='fa fa-car'></i>",
				"fa fa-eye|<i class='fa fa-eye'></i>",
				"fa fa-area-chart|<i class='fa fa-area-chart'></i>",
				"fa fa-envelope|<i class='fa fa-envelope'></i>",
				"fa fa-tag|<i class='fa fa-tag'></i>",
				"fa fa-truck|<i class='fa fa-truck'></i>",
				"fa fa-trash|<i class='fa fa-trash'></i>",
				"fa fa-tasks|<i class='fa fa-tasks'></i>",
				"fa fa-cube|<i class='fa fa-cube'></i>",				
				"fa fa-dashboard|<i class='fa fa-dashboard'></i>",				
				"fa fa-folder-open|<i class='fa fa-folder-open'></i>",
				"fa fa-credit-card|<i class='fa fa-credit-card'></i>",
				"fa fa-inbox|<i class='fa fa-inbox'></i>",								
				"fa fa-info-circle|<i class='fa fa-info-circle'></i>",
				"fa fa-cloud-download|<i class='fa fa-cloud-download'></i>",
			),"value"=>"fa fa-bars");

		$url_find_sorting = url(config('crudbooster.ADMIN_PATH')).'/module_generator/find-last-sorting';

		if(Request::segment(3)=='add') {
			$this->form[] = array("label"=>"Sorting","name"=>"sorting_group","jquery"=>"
				$.get('$url_find_sorting',function(resp) {
					$('#sorting_group').val(resp);
				});
			");
		}else{
			$this->form[] = array('label'=>'Sorting','name'=>'sorting_group');
		}
		

		$this->form[] = array("label"=>"Is Group","name"=>"is_group","type"=>"radio","dataenum"=>array("1|Yes","0|No"),'value'=>1);
		
		$this->sub_module[] = array('label'=>'Modules','path'=>"cms_moduls","icon"=>"fa fa-cog","foreign_key"=>"id_cms_moduls_group"); 

		$this->addaction[] = array('label'=>'Up','route'=>url(config('crudbooster.ADMIN_PATH')).'/module_generator/arr-sorting/[id]/up','icon'=>'fa fa-arrow-up','ajax'=>true);
		$this->addaction[] = array('label'=>'Down','route'=>url(config('crudbooster.ADMIN_PATH')).'/module_generator/arr-sorting/[id]/down','icon'=>'fa fa-arrow-down','ajax'=>true);

		$this->constructor();
	}

	public function hook_before_add(&$postdata) {
		$sorting_group = $postdata['sorting_group'];
		$data = DB::table($this->table)->where('sorting_group','>=',$sorting_group)->get();
		foreach($data as $d) {			
			DB::table($this->table)->where($this->primary_key,$d->id)->update(['sorting_group'=> $d->sorting_group + 1  ]);
		}
	}


	public function getFindLastSorting() {
		$ro = DB::table('cms_moduls_group')->count();
		$ro = $ro + 1;
		echo $ro;
	}

	public function getArrSorting($id,$tipe) {
		$row = DB::table('cms_moduls_group')->where('id',$id)->first();

		if($tipe=='down') {
			$new_sorting = $row->sorting_group + 1;
		}else{
			$new_sorting = $row->sorting_group - 1;
		}		

		$new_sorting = ($new_sorting<=0)?1:$new_sorting;

		DB::table('cms_moduls_group')->where('sorting_group',$new_sorting)->update(array('sorting_group'=>$row->sorting_group));
		DB::table('cms_moduls_group')->where('id',$id)->update(array('sorting_group'=>$new_sorting));

		return redirect()->back()->with(['message'=>"Sort data success !",'message_type'=>'success']);
	}

}
