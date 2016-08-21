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

class NotificationsController extends CBController {

    public function __construct() {
        $this->table              = "cms_notifications";
        $this->primary_key        = "id";
        $this->title_field        = "content";
        $this->limit              = 20;
        $this->index_orderby      = ["id"=>"desc"];
        $this->button_show_data   = true;
        $this->button_reload_data = true;
        $this->button_new_data    = false;
        $this->button_delete_data = true;
        $this->button_sort_filter = true;        
        $this->button_export_data = true;

        $read_notification_url = url(config('crudbooster.ADMIN_PATH')).'/notifications/read';

        $this->col = array();		
        $this->col[] = array("label"=>"Icon","name"=>"icon","width"=>"3%","callback_php"=>'"<i class=\'$row->icon\'></i>"');
		$this->col[] = array("label"=>"Content","name"=>"content","callback_php"=>'"<a href=\"'.$read_notification_url.'/$row->id\">$row->content</a>"' );	
        $this->col[] = array('label'=>'Read','name'=>'is_read','callback_php'=>'($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"');	

		$this->form = array();		
		$this->form[] = array("label"=>"Content","name"=>"content","type"=>"text"   );
		$this->form[] = array("label"=>"Icon","name"=>"icon","type"=>"text"   );
		$this->form[] = array("label"=>"Notification Command","name"=>"notification_command","type"=>"textarea"   );
		$this->form[] = array("label"=>"Is Read","name"=>"is_read","type"=>"text"   );
     
        
        //You may use this bellow array to add alert message to this module at overheader
        $this->alert        = array();
        
        //You may use this bellow array to add more your own header button 
        $this->index_button = array();            
        
        //You may use this bellow array to add relational data to next tab 
        $this->form_tab     = array();
        
        //You may use this bellow array to add relational data to next area or element, i mean under the existing form 
        $this->form_sub     = array();
        
        //You may use this bellow array to add some or more html that you want under the existing form 
        $this->form_add     = array();                                                                                      
        
        //You may use this bellow array to add statistic at dashboard 
        $this->index_statistic = array();

        //No need chanage this constructor
        $this->constructor();
    }

    public function getLatestJson() {
        $rows = DB::table('cms_notifications')
        ->where('id_cms_users',0)
        ->orWhere('id_cms_users',get_my_id())
        ->orderby('id','desc')
        ->where('is_read',0)->take(25)->get();
        $total = count($rows);
        return response()->json(['items'=>$rows,'total'=>$total]);
    }

    public function getRead($id) {
        DB::table('cms_notifications')->where('id',$id)->update(['is_read'=>1]);
        $row = DB::table('cms_notifications')->where('id',$id)->first();
        $command = $row->notification_command;
        $command = json_decode($command);

        if($command) {
            switch($command->type){
                default:
                case 'link':
                    if(@$command->value) return redirect($command->value);
                break;
                case 'module':
                    $value = $command->value;
                    switch ($command->action) {
                        case 'create':
                            $action = 'add';                            
                        break;
                        case 'read':
                            $action = 'detail/'.$value->id;
                        break;
                        case 'update':
                            $action = 'edit/'.$value->id;
                        break;
                        case 'delete':
                            $action = 'delete/'.$value->id;
                        break;
                    }
                    $link = url(config('crudbooster.ADMIN_PATH').'/'.$value->permalink.'/'.$action);
                    return redirect($link);
                break;
            }
        }else{
            return redirect()->route('NotificationsControllerGetIndex');
        }        
    }


    public function hook_before_index(&$result) {
        //Use this hook for manipulate query of index result 
        
    }
    public function hook_html_index(&$html,$data) {
        //Use this hook for manipulate result of html in index 

    }
    public function hook_before_add(&$arr) {
        //Use this hook for manipulate data input before add data is execute 

    }
    public function hook_after_add($id) {
        //Use this hook if you want execute other command after add function called 

    }
    public function hook_before_edit(&$arr,$id) {
        //Use this hook for manipulate data input before update data is execute 

    }
    public function hook_after_edit($id) {
        //Use this hook if you want execute other command after update data called 

    }
    public function hook_before_delete($id) {
        //Use this hook if you want execute other command before delete command called 

    }
    public function hook_after_delete($id) {
        //Use this hook if you want execute other command after delete command called 

    }
    
}