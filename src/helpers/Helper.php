<?php 
/* 
| ---------------------------------------------------------------------------------------------------------------
| Main Helper of CRUDBooster
| Do not edit or modify this helper unless your modification will be replace if any update from CRUDBooster.
| If you want add new helper please refer to CustomHelper.php next this file.
| 
| Homepage : http://crudbooster.com
| ---------------------------------------------------------------------------------------------------------------
|
*/

/* 
| --------------------------------------------------------------------------------------------------------------
| To get diffrence minute between date
| --------------------------------------------------------------------------------------------------------------
| $dateFrom = path of route
| $dateTo   = controller name
|
*/

if(!function_exists('diff_minute')) {
    function diff_minute($dateFrom,$dateTo='NOW') {

        $dateTo = ($dateTo == 'NOW')?date('Y-m-d H:i:s'):$dateTo;

        $to_time = strtotime($dateTo);
        $from_time = strtotime($dateFrom);
        return round(abs($to_time - $from_time) / 60,2);
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Alternate route for Laravel Route::controller
| --------------------------------------------------------------------------------------------------------------
| $prefix       = path of route
| $controller   = controller name
| $namespace    = namespace of controller (optional)
|
*/ 
if(!function_exists('RouteController')) {
    function RouteController($prefix,$controller,$namespace=NULL) {        

        $prefix = trim($prefix,'/').'/';

        $namespace = ($namespace)?:'App\Http\Controllers';

        Route::get($prefix,['uses'=>$controller.'@getIndex','as'=>$controller.'GetIndex']);
        $controller_class = new ReflectionClass($namespace.'\\'.$controller);                          
        $controller_methods = $controller_class->getMethods(ReflectionMethod::IS_PUBLIC);
        $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';         
        foreach($controller_methods as $method) {
            if ($method->class != 'Illuminate\Routing\Controller' && $method->name != 'getIndex') {                                             
                if(substr($method->name, 0, 3) == 'get') {
                    $method_name = substr($method->name, 3);
                    $slug = array_filter(preg_split('/(?=[A-Z])/',$method_name));   
                    $slug = strtolower(implode('-',$slug));
                    $slug = ($slug == 'index')?'':$slug;
                    Route::get($prefix.$slug.$wildcards,['uses'=>$controller.'@'.$method->name,'as'=>$controller.'Get'.$method_name] );
                }elseif(substr($method->name, 0, 4) == 'post') {
                    $method_name = substr($method->name, 4);
                    $slug = array_filter(preg_split('/(?=[A-Z])/',$method_name));                                   
                    Route::post($prefix.strtolower(implode('-',$slug)).$wildcards,['uses'=>$controller.'@'.$method->name,'as'=>$controller.'Post'.$method_name] );
                }
            }                   
        }
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Send Push Notification to Backend
| --------------------------------------------------------------------------------------------------------------
| $content      = message (required)
| $icon         = font awesome icon (optional)
| $type         = message type warning, success, danger, info (optional)
| $command      = array of command (optional)
| ['type'=>'link','value'=>'URL'] or ['type'=>'module,'value'=>['permalink'=>'logs','action'=>'create,read,update,delete','id'=>1]]
| $id_cms_users = id current users for default or id user destination (optional)
|
*/ 

if(!function_exists('push_notification')) {
    function push_notification($content,$icon='fa fa-warning',$type='warning',$command=array(),$id_cms_users=NULL) {
        $id_cms_users = ($id_cms_users)?:get_my_id();
        switch ($type) {
            case 'warning':
                $icon .= ' text-warning';
                break;
            case 'danger':
                $icon .= ' text-danger';
            break;
            case 'success':
                $icon .= ' text-success';
            break;
            case 'info':
                $icon .= ' text-info';
            break;
            default:
                $icon .= ' text-warning';
                break;
        }

        $a                         = array();
        $a['created_at']           = date('Y-m-d H:i:s');
        $a['id_cms_users']         = $id_cms_users;
        $a['content']              = $content;
        $a['icon']                 = $icon;
        $a['notification_command'] = json_encode($command);
        $a['is_read']              = 0;
        if(DB::table('cms_notifications')->insert($a)) return true;
        else return false;
    }
}


/* 
| --------------------------------------------------------------------------------------------------------------
| Sending GCM Push Notification
| --------------------------------------------------------------------------------------------------------------
| $regid     = registration id from google
| $datae     = data array
| $googlekey = google api key
*/
if(!function_exists('send_gcm')) {
function send_gcm($regid,$data,$google_key=NULL){
    $google_api_key = ($googlekey)?:config('crudbooster.GOOGLE_API_KEY');
    $url = 'https://android.googleapis.com/gcm/send';
    $fields = array(
      'registration_ids' => $regid,
      'data' => $data,
    );
    $headers = array(
      'Authorization:key=' . $google_key,
      'Content-Type:application/json'
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $chresult = json_decode(curl_exec($ch));
    curl_close($ch);
    return $chresult;
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Generate Auto Menu
| --------------------------------------------------------------------------------------------------------------
| $slug = slug menu 
*/
if(!function_exists('generate_menu')) {
function generate_menu($slug,$parent_id=0) {
    $menus = DB::table('cms_menus')
    ->join('cms_menus_groups','cms_menus_groups.id','=','id_cms_menus_groups')
    ->where('cms_menus_groups.slug',$slug)
    ->where('cms_menus.parent_id_cms_menus',$slug)
    ->select('cms_menus.*')
    ->get();

    $class = ($parent_id==0)?'menu_crudbooster_'.$slug:'submenu_crudbooster_'.$slug;
    echo "<ul class='$class'>";
    foreach($menus as $menu) {
        $label = $menu->name;
        switch($menu->menu_type) {
            case 'Custom Link':
                $link = str_replace('[domain]',url('/'),$menu->menu_link);
            break;
            case 'Posts':
                $posts = DB::table('cms_posts')->where('id',$menu->id_cms_posts)->first();
                $link = url('view/'.$posts->slug);
            break;
            case 'Pages':
                $pages = DB::table('cms_pages')->where('id',$menu->id_cms_pages)->first();
                $link = url('page/'.$pages->slug);
            break;
            case 'Categories':
                $categories = DB::table('cms_posts_categories')->where('id',$menu->id_cms_posts_categories)->first();
                $link = url('category/'.$categories->slug);
            break;
        }

        $sub_count = DB::table('cms_menus')->where('parent_id_cms_menus',$menu->id)->count();
        if($sub_count>0) {
            echo "<li><a href='$link'>$label</a>";
            generate_menu($slug,$menu->id);
            echo "</li>";
        }else{
            echo "<li><a href='$link'>$label</a></li>";
        }        
    }

    echo "</ul>";
}
}

/*
| -------------------------------------------------------------------------------------------------------------- 
| Auto create scafollding input for where especially for form tab 
| --------------------------------------------------------------------------------------------------------------
| $name = field name
*/ 
if(!function_exists('get_where_value')) {
function get_where_value($name) {
    $input = array("label"=>"cms menus groups","name"=>"id_cms_menus_groups","type"=>"hidden","value"=>\Request::get('where')[$name]);
    return $input;
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get columns from a table 
| --------------------------------------------------------------------------------------------------------------
| $table = table name 
*/
if(!function_exists('get_columns_table')) {
function get_columns_table($table) {
    $cols = DB::getSchemaBuilder()->getColumnListing($table);
    $result = array();
    $result = $cols;

    $new_result = array(); 
    foreach($result as $ro) {
        if($ro=='created_at' || $ro=='updated_at' || $ro=='id') continue;
        $new_result[] = $ro;
    }
    return $new_result;
}
}

/*
| -------------------------------------------------------------------------------------------------------------- 
| Get title field candidate 
| --------------------------------------------------------------------------------------------------------------
| $columns = array of columns
*/
if(!function_exists('get_namefield_table')) {
function get_namefield_table($columns) {
    $name_col_candidate = array("name","nama","title","judul","content");   
    $name_col = '';
    foreach($columns as $c) {
        foreach($name_col_candidate as $cc) {
            if( strpos($c,$cc) !==FALSE ) {
                $name_col = $c;
                break;
            }
        }
        if($name_col) break;
    }
    if($name_col == '') $name_col = 'id';
    return $name_col;
}
}

/*
| --------------------------------------------------------------------------------------------------------------
| To check wheter controller exists or not 
| --------------------------------------------------------------------------------------------------------------
| $table = table name
*/
if(!function_exists('is_exists_controller')) {
function is_exists_controller($table) {
    $controllername = ucwords(str_replace('_',' ',$table));
    $controllername = str_replace(' ','',$controllername).'Controller';
    $path = base_path("app/Http/Controllers/");
    $path2 = base_path("app/Http/Controllers/ControllerMaster/");
    if(file_exists($path.'Admin'.$controllername.'.php') || file_exists($path2.'Admin'.$controllername.'.php') || file_exists($path2.$controllername.'.php')) {
        return true;
    }else{
        return false;
    }
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Generate an API
| --------------------------------------------------------------------------------------------------------------
| $table = table name 
| $name  = custom controller 
|
*/
if(!function_exists('generate_api')) {
function generate_api($controller_name,$table_name,$permalink,$type) {
    $php = '
<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use Mail;
use Hash;
use Cache;
use Validator;

class Api'.$controller_name.'Controller extends \crocodicstudio\crudbooster\controllers\ApiController {

    function __construct() {    
        $this->table     = "'.$table_name.'";        
        $this->permalink = "'.$permalink.'";        
    }
';

$php .= "\n".'
    public function hook_before(&$postdata) {
        //Code here if you want execute some action before API Query Called
    }';


$php .= "\n".'
    public function hook_after($postdata,&$result) {
        //Code here if you want execute some action after API Query Called
    }';

$php .= "\n".'
    public function hook_query_list(&$data) {
        //Code here if you want execute some action while API Database Query especially for Listing Type of API
    }';

$php .= "\n".'
    public function hook_query_detail(&$data) {
        //Code here if you want execute some action while API Database Query especially for Detail Type of API
    }';

$php .= "\n".'
}
';

        $php = trim($php);
        $path = base_path("app/Http/Controllers/");
        file_put_contents($path.'Api'.$controller_name.'Controller.php', $php);
}
}


/* 
| --------------------------------------------------------------------------------------------------------------
| Generate a New Controller from table 
| --------------------------------------------------------------------------------------------------------------
| $table = table name 
| $name  = custom controller 
|
*/
if(!function_exists('generate_controller')) {
function generate_controller($table,$name='') { 
        
        $exception          = ['slug'];
        $image_candidate    = explode(',',env('IMAGE_FIELDS_CANDIDATE','image,picture,photo,photos,foto,gambar,thumbnail'));
        $password_candidate = explode(',',env('PASSWORD_FIELDS_CANDIDATE','password,pass,pwd,passwrd,sandi,pin'));
        $phone_candidate    = explode(',',env('PHONE_FIELDS_CANDIDATE','phone,telp,hp,notelp,no_telp,no_phone,phone_number'));
        $email_candidate    = explode(',',env('EMAIL_FIELDS_CANDIDATE','email,mail,email_address,mail_address'));
        $name_candidate     = explode(',',env('NAME_FIELDS_CANDIDATE','name,nama,person_name,person,fullname,full_name,nickname,nick,nick_name'));
        $url_candidate      = explode(',',env("URL_FIELDS_CANDIDATE",'url,link'));


        $controllername = ucwords(str_replace('_',' ',$table));        
        $controllername = str_replace(' ','',$controllername).'Controller';
        if($name) {
            $controllername = ucwords(str_replace(array('_','-'),' ',$name));            
            $controllername = str_replace(' ','',$controllername).'Controller';
        }

        $path = base_path("app/Http/Controllers/");        

        if(file_exists($path.'Admin'.$controllername.'.php')) {
            return 'Admin'.$controllername;
            exit;
        }
        $coloms   = get_columns_table($table);
        $name_col = get_namefield_table($coloms);
                
$php = '
<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use Mail;
use Hash;
use Cache;
use Validator;

class Admin'.$controllername.' extends \crocodicstudio\crudbooster\controllers\CBController {

    public function __construct() {
        $this->table              = "'.$table.'";
        $this->primary_key        = "id";
        $this->title_field        = "'.$name_col.'";
        $this->limit              = 20;
        $this->index_orderby      = ["id"=>"desc"];
        $this->button_show_data   = true;
        $this->button_reload_data = true;
        $this->button_new_data    = true;
        $this->button_delete_data = true;
        $this->button_sort_filter = true;        
        $this->button_export_data = true;

        $this->col = array();
';

        foreach($coloms as $c) {
            $label = str_replace("id_","",$c);
            $label = ucwords(str_replace("_"," ",$label));
            $field = $c;

            if(in_array($field, $exception)) continue;

            if(substr($field,0,3)=='id_') {
                $jointable = str_replace('id_','',$field);
                $joincols = get_columns_table($jointable);
                $joinname = get_namefield_table($joincols);
                $php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","name"=>"'.$field.'","join"=>"'.$jointable.','.$joinname.'");'."\n";
            }else{
                $image = '';
                if(in_array($field, $image_candidate)) $image = ',"image"=>true';
                $php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","name"=>"'.$field.'" '.$image.');'."\n";    
            }
        }

        $php .= "\n\t\t".'$this->form = array();'."\n";

        foreach($coloms as $c) {
            $attribute    = array();
            $validation   = array();
            $validation[] = 'required';            
            $placeholder  = '';
            $help         = '';

            $label = str_replace("id_","",$c);
            $label = ucwords(str_replace("_"," ",$label));      
            $field = $c;

            if(in_array($field, $exception)) {
                $php .= "\t\t".'$this->form[] = array("name"=>"'.$field.'","type"=>"hidden");'."\n";
                continue;
            }
            

            try{
                 $typedata = DB::connection()->getDoctrineColumn($table, $field)->getType()->getName();
            }
            catch(\Exception $e){
                //MySQL
                $the_field       = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = \''.$field.'\''))[0];
                $col_type        = $the_field->Type;
                preg_match( '/([a-z]+)\((.+)\)/', $col_type, $match );
                $typedata        = $match[1];
                $typedata_length = $match[2];
            }

            switch($typedata) {
                default:
                case 'varchar':
                case 'char':
                $type = "text";
                $validation[] = "min:3|max:255";                
                break;
                case 'text':
                case 'longtext':
                $type = 'textarea';
                $validation[] = "string|min:5|max:5000";                
                break;
                case 'date':
                $type = 'date';
                $validation[] = "date";
                break;
                case 'datetime':
                case 'timestamp':
                $type = 'datetime';
                $validation[] = "date_format:Y-m-d H:i:s";
                break;
                case 'time':
                $type = 'time';
                $validation[] = 'date_format:H:i:s';
                break;
                case 'double':
                $type = 'money';
                $validation[] = "numeric";
                break;
                case 'enum':
                $type = 'radio';                                    
                $attribute['dataenum'] = "array(".$typedata_length.")";         
                break;
                case 'int':
                case 'integer':
                $validation[] = 'integer';
                break;
            }
                       
            if(substr($field,0,3)=='id_') {
                $jointable = str_replace('id_','',$field);
                $joincols = get_columns_table($jointable);
                $joinname = get_namefield_table($joincols);
                $attribute['datatable'] = $jointable.','.$joinname;
                $type = 'select';
            }

            if(in_array($field, $password_candidate)) {
                $type = 'password';
                $validation = ['min:5','max:32'];
                $attribute['help'] = "Minimum 5 characters. Please leave empty if you did not change the password.";                
            }

        
            if(in_array($field, $image_candidate)) {
                $type = 'upload';                
                $attribute['help'] = "File types support : JPG, JPEG, PNG, GIF, BMP";
                $attribute['upload_file'] = FALSE;
            }           

            if($field == 'latitude' || $field == 'longitude') {
                $type = 'hidden';      
                $attribute['googlemaps'] = TRUE;      
            }

            if(in_array($field, $phone_candidate)) {
                $type = 'number';
                $validation = ['required','min:3','numeric'];
                $attribute['placeholder'] = "You can only enter the number only";
            }

            if(in_array($field, $email_candidate)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $attribute['placeholder'] = "Please enter a valid email address";
            }

            if($type=='text' && in_array($field, $name_candidate)) {
                $validation[] = 'alpha_spaces';    
                $attribute['placeholder'] = "You can only enter the letter only";            
            }

            if($type=='text' && in_array($field, $url_candidate)) {
                $validation[] = 'url';
                $attribute['placeholder'] = "Please enter a valid URL";
            }

            $validation = implode('|',$validation);

            $php .= "\t\t";
            $php .= '$this->form[] = array("label"=>"'.$label.'","name"=>"'.$field.'","type"=>"'.$type.'","required"=>TRUE';
            
            if($validation) $php .= ',"validation"=>"'.$validation.'"';            

            if($attribute) {
                foreach($attribute as $key=>$val) {
                    if(is_bool($val)) {
                        $val = ($val)?"TRUE":"FALSE";
                    }else{
                        if(strpos($val, "array(")!==FALSE) {
                            $val = $val;
                        }else{
                            $val = '"'.$val.'"';
                        }                        
                    }
                    $php .= ',"'.$key.'"=>'.$val;
                }
            }

            $php .= ");\n";            
        }

$php .= '     
        
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

    //By the way, you can still create your own method in here... :)
    
}
        ';

        $php = trim($php);

        //create file controller
        file_put_contents($path.'Admin'.$controllername.'.php', $php);
        return 'Admin'.$controllername;
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get Type Name of Field
| --------------------------------------------------------------------------------------------------------------
| $table    = table name
| $field    = field name
|
*/
if(!function_exists('get_field_type')) {
function get_field_type($table,$field) {
    if(Cache::has('field_type_'.$table.'_'.$field)) {
        return Cache::get('field_type_'.$table.'_'.$field);
    }
    
    $typedata = Cache::rememberForever('field_type_'.$table.'_'.$field,function() use ($table,$field) {
        try{
             $typedata = DB::connection()->getDoctrineColumn($table, $field)->getType()->getName();
        }
        catch(\Exception $e){
            //MySQL
            $the_field       = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = \''.$field.'\''))[0];
            $col_type        = $the_field->Type;
            preg_match( '/([a-z]+)\((.+)\)/', $col_type, $match );
            $typedata        = $match[1];
            $typedata_length = $match[2];
        }
        return $typedata;
    });

    return $typedata;
}
}

/* 
| ----------------------------------------------------
| Get Value Filter
| ----------------------------------------------------
| $field = field name
|
*/
if(!function_exists('get_value_filter')) {
function get_value_filter($field) {
    $filter = Request::get('filter_column');
    if($filter[$field]) {
        return $filter[$field]['value'];
    }
}
}

/* 
| ----------------------------------------------------
| Get Type Filter
| ----------------------------------------------------
| $field = field name
|
*/
if(!function_exists('get_type_filter')) {
function get_type_filter($field) {
    $filter = Request::get('filter_column');
    if($filter[$field]) {
        return $filter[$field]['type'];
    }
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get string between words
| --------------------------------------------------------------------------------------------------------------
| $string = string 
| $start  = start string
| #end    = end string
|
*/
if(!function_exists('get_string_between')) {
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Generate unique array from multidimensional arrays
| --------------------------------------------------------------------------------------------------------------
| $array = array 
| $key   = what key for unique
|
*/
if(!function_exists('super_unique')) {
function super_unique($array,$key)
{
   $temp_array = array();
   foreach ($array as &$v) {
       if (!isset($temp_array[$v[$key]]))
       $temp_array[$v[$key]] =& $v;
   }
   $array = array_values($temp_array);
   return $array;
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Generate words about timelapsed
| --------------------------------------------------------------------------------------------------------------
| $datetime_to = date and time
| $datetime_from = date and time from (default now)
| $full = true / false
|
*/
if(!function_exists('time_elapsed_string')) {
function time_elapsed_string($datetime_to,$datetime_from=NULL, $full = false) {
    $datetime_from = ($datetime_from)?:date('Y-m-d H:i:s');
    $now = new DateTime;
    if($datetime_from!='') {
        $now = new DateTime($datetime_from);
    }
    $ago = new DateTime($datetime_to);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ' : 'just now';
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Generate words about timelapsed
| --------------------------------------------------------------------------------------------------------------
| $datetime_to = date and time
| $datetime_from = date and time from (default now)
| $full = true / false
|
*/
if(!function_exists('human_filesize')) {
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get file size from a file/url in bytes
| --------------------------------------------------------------------------------------------------------------
| $url = url of file
|
*/
if(!function_exists('get_size')) {
function get_size($url) {
    $head = array_change_key_case(get_headers($url, TRUE));
    return $head['content-length'];
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To send email 
| --------------------------------------------------------------------------------------------------------------
| $to       = email destination
| $subject  = subject of email
| $html     = content of email 
| $from     = default 'no-reply@SERVER_NAME' 
| $template = default 'emails.blank'
|
*/ 
if(!function_exists('send_email')) {
function send_email($to,$subject,$html,$from='',$template='') {
     $setting = DB::table('cms_settings')->where('name','like','smtp%')->get();
     $set = array();
     foreach($setting as $s) {
        $set[$s->name] = $s->content;
     }     

    \Config::set('mail.driver',$set['smtp_driver']);
    \Config::set('mail.host',$set['smtp_host']);
    \Config::set('mail.port',$set['smtp_port']);
    \Config::set('mail.username',$set['smtp_username']);
    \Config::set('mail.password',$set['smtp_password']);

    $template = ($template)?:"crudbooster::emails.blank";
    $from = ($from)?:$set['smtp_username'];
    $from = ($from)?:"no-reply@".$_SERVER['SERVER_NAME'];
    $data['content'] = $html;
    \Mail::send($template,$data,function($message) use ($to,$subject,$from) {
        $message->to($to);
        $message->from($from);
        $message->subject($subject);
    });
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To send email with queues
| --------------------------------------------------------------------------------------------------------------
| $to       = email destination
| $subject  = subject of email
| $content  = content of email 
| $send_at  = DateTime to send the email
| $cc       = cc email
|
*/
if(!function_exists('send_email_queue')) {
    function send_email_queue($to,$subject,$content,$send_at=NULL,$cc=NULL) {
         $a = array();
         $a['send_at'] = isset($send_at)?date('Y-m-d H:i:s',strtotime($send_at)):date('Y-m-d H:i:s');
         $a['email_recipient'] = $to;
         if(isset($cc)) $a['email_cc'] = $cc;
         $a['email_subject'] = $subject;
         $a['email_content'] = $content;
         $a['is_sent'] = 0;
         DB::table('cms_email_queues')->insert($a);
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To un parse the url
| --------------------------------------------------------------------------------------------------------------
| $parsed_url = parsed url
|
*/ 
if(!function_exists('unparse_url')) {
function unparse_url($parsed_url) { 
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
  $pass     = ($user || $pass) ? "$pass@" : ''; 
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
  return urldecode("$scheme$user$pass$host$port$path$query$fragment"); 
}
}

if(!function_exists('show_value')) {
    function show_value($id,$tabel,$show='value',$empty=''){

        $queries = DB::table($tabel)
            ->where('id','=',$id)
            ->orderBy('id','DESC')
            ->first();

        if(empty($queries))
        {
            $the_value =  $empty;
        } else {
            $the_value =  $queries->$show;
        }

        return $the_value;          
    }
}

if(!function_exists('showValue_byField')) {
    function showValue_byField($field,$value,$table,$show='value',$empty=''){

        $queries = DB::table($table)
            ->where($field,'=',$value)
            ->orderBy('id','DESC')
            ->first();

        if(empty($queries))
        {
            $the_value =  $empty;
        } else {
            $the_value =  $queries->$show;
        }

        return $the_value;          
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To get the setting
| --------------------------------------------------------------------------------------------------------------
| $name = name of setting
|
*/ 
if(!function_exists('get_setting')) {
function get_setting($name){
    if(Schema::hasTable('cms_settings')) {
        if(Cache::has('setting_'.$name)) {
            return Cache::get('setting_'.$name);
        }else{                
            $value = Cache::rememberForever('setting_'.$name,function() use ($name) {
                $query = DB::table('cms_settings')->where('name',$name)->first();
                return $query->content;
            });
            return $value;
        }
    }        
}
}

if(!function_exists('array_table')) {
function array_table($table,$orderby='id',$empty=''){
    $query = DB::table($table)
        ->orderby($orderby,'ASC')
        ->get();

    if (empty($query)) {
        $result = $empty;
    }else{
        $result = $query;
    }

    return $result;
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To make a slug at a table if 'slug' field is already
| --------------------------------------------------------------------------------------------------------------
| $title = title of content
| $table = table name
| $where = default value is 'title'
| $id    = for id of table
|
*/
if(!function_exists('slug')) {
function slug($title,$table,$where="title",$id=NULL){
    $slug_title = str_slug($title, "-");

    $queries = DB::table($table)
        ->where($where,'=',$title)  
        ->where('slug',$slug_title)      
        ->orderBy('id','DESC');
    if($id) {
        $queries->where('id','!=',$id);
    }
    $queries = $queries->first();


    if(is_null($queries)){
        $slug      = trim(preg_replace('/[^a-z0-9]+/i', '-', $slug_title), '-');
        $the_value =  strtolower($slug);
    }else{  
        $string    = $queries->$where;
        $slug      = trim(preg_replace('/[^a-z0-9]+/i', '-', $string), '-');    
        $lastplust = substr(strrchr($slug, '-'), 1)+1;
        $the_value = strtolower($slug_title."-".$lastplust);

    }
    return $the_value;

}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/
if(!function_exists('g')) {
function g($name) {
    return Request::get($name);
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To validation input data more easy . 
| --------------------------------------------------------------------------------------------------------------
| $arr = array like laravel validation array definition
| $type = type of response json or use in view
|
*/
if(!function_exists('valid')) {
function valid($arr=array(),$type='json') {
    $input_arr = Request::all();

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

        if($type == 'json') {
            $result = array();      
            $result['api_status'] = 0;
            $result['api_message'] = implode(', ',$message);
            $res = response()->json($result,400);
            $res->send();
            exit;
        }else{            
            return redirect()->back()->with(['message'=>implode(', ',$message),'message_type'=>"warning"]);
        }        
    }
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To response the json more easy
| --------------------------------------------------------------------------------------------------------------
| $status = boolean value
| $message = message of api
| $data = data of api 
|
*/
if(!function_exists('response_json')) {
function response_json($status,$message=NULL,$data=array()) {
    if(!$status && !is_array($status)) {
        $r = array();
        $r['api_status'] = 0;
        $r['api_message'] = $message?:"Failed";           
        $res = response()->json($r,200);
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
        $r['api_message'] = $message?:'success';
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

/* 
| --------------------------------------------------------------------------------------------------------------
| To get all url parameters
| --------------------------------------------------------------------------------------------------------------
| 
*/
if(!function_exists('get_input_url_parameters')) {
function get_input_url_parameters($exception=NULL) {
    @$get = $_GET;    
    $inputhtml = '';

    if($get) {

        if(is_array($exception)) {
            foreach($exception as $e) {
                unset($get[$e]);
            }
        }        

        $string_parameters = http_build_query($get);
        $string_parameters_array = explode('&',$string_parameters);
        foreach($string_parameters_array as $s) {
            $part = explode('=',$s);
            $name = urldecode($part[0]);            
            $inputhtml .= "<input type='hidden' name='$name' value='$part[1]'/>";
        }                                                           
    }

    return $inputhtml;                        
}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| These bellow are simpily function to get crudbooster session
| --------------------------------------------------------------------------------------------------------------
| 
|
*/
if(!function_exists('get_my_id')) {
    function get_my_id() {
        return Session::get('admin_id');
    }
}

if(!function_exists('get_my_id_company')) {
    function get_my_id_company() {
        return Session::get('admin_id_companies');
    }
}

if(!function_exists('get_is_superadmin')) {
    function get_is_superadmin() {
        return Session::get('admin_is_superadmin');
    }
}

if(!function_exists('get_my_name')) {
    function get_my_name() {
        return Session::get('admin_name');
    }
}

if(!function_exists('get_my_photo')) {
    function get_my_photo() {
        return Session::get('admin_photo');
    }
}

if(!function_exists('get_my_id_privilege')) {
    function get_my_id_privilege() {
        return Session::get('admin_privileges');
    }
}

if(!function_exists('get_my_privilege_name')) {
    function get_my_privilege_name() {
        return Session::get('admin_privileges_name');
    }
}

if(!function_exists('get_is_locked')) {
    function get_is_locked() {
        return Session::get('admin_lock');
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To get method name
| --------------------------------------------------------------------------------------------------------------
| will be return method name . e.g : getIndex
|
*/
if(!function_exists('get_method')) {
    function get_method() {
        $action = str_replace("App\Http\Controllers","",Route::currentRouteAction());
        $atloc = strpos($action, '@')+1;
        $method = substr($action, $atloc);
        return $method;
    }
}


/* 
| --------------------------------------------------------------------------------------------------------------
| To get row id
| --------------------------------------------------------------------------------------------------------------
| will be return current row id of edit
|
*/
if(!function_exists('get_row_id')) {
    function get_row_id() {
        $id = Session::get('current_row_id');
        $id = intval($id);
        return $id;
    }
}


/* 
| --------------------------------------------------------------------------------------------------------------
| To get module path
| --------------------------------------------------------------------------------------------------------------
| will be return module path
|
*/
if(!function_exists('get_module_path')) {
    function get_module_path() {
        $path = Request::segment(2);        
        return $path;
    }
}


/* 
| --------------------------------------------------------------------------------------------------------------
| To get privilege
| --------------------------------------------------------------------------------------------------------------
| will be return privilege
|
*/
if(!function_exists('privilege_is_visible')) {
    function privilege_is_visible() {
        $privileges = DB::table("cms_privileges_roles")
                    ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
                    ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
                    ->where("cms_moduls.path",get_module_path())->first();
        return ($privileges && $privileges->is_visible)?true:false;

    }
}

if(!function_exists('privilege_is_read')) {
    function privilege_is_read() {
        $privileges = DB::table("cms_privileges_roles")
                    ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
                    ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
                    ->where("cms_moduls.path",get_module_path())->first();
        return ($privileges && $privileges->is_read)?true:false;

    }
}

if(!function_exists('privilege_is_create')) {
    function privilege_is_create() {
        $privileges = DB::table("cms_privileges_roles")
                    ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
                    ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
                    ->where("cms_moduls.path",get_module_path())->first();
        return ($privileges && $privileges->is_create)?true:false;

    }
}

if(!function_exists('privilege_is_update')) {
    function privilege_is_update() {
        $privileges = DB::table("cms_privileges_roles")
                    ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
                    ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
                    ->where("cms_moduls.path",get_module_path())->first();
        return ($privileges && $privileges->is_update)?true:false;

    }
}

if(!function_exists('privilege_is_delete')) {
    function privilege_is_delete() {
        $privileges = DB::table("cms_privileges_roles")
                    ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
                    ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
                    ->where("cms_moduls.path",get_module_path())->first();
        return ($privileges && $privileges->is_delete)?true:false;

    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To get main path url
| --------------------------------------------------------------------------------------------------------------
| will be return url get index of current
|
*/
if(!function_exists('mainpath')) {
    function mainpath($path=NULL) {
        $path = ($path)?"/$path":"";
        $controllername = str_replace(["\crocodicstudio\crudbooster\controllers\\","App\Http\Controllers\\"],"",strtok(Route::currentRouteAction(),'@') );      
        $route_url = route($controllername.'GetIndex');     
        return $route_url.$path;        
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| To log activity
| --------------------------------------------------------------------------------------------------------------
| $description = describe of activity
|
*/
if(!function_exists('insert_log')) {
    function insert_log($description) {
        $a                 = array();
        $a['created_at']   = date('Y-m-d H:i:s');
        $a['ipaddress']    = $_SERVER['REMOTE_ADDR'];
        $a['useragent']    = $_SERVER['HTTP_USER_AGENT'];
        $a['url']          = Request::url();
        $a['description']  = $description;
        $a['id_cms_users'] = get_my_id();
        DB::table('cms_logs')->insert($a);    
    }
}
