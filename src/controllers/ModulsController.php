<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
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

class ModulsController extends CBController {
	
	public function __construct(Request $request) {		
		$this->table               = 'cms_moduls';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->limit               = 100;
		$this->button_export       = false;
		$this->button_import       = false;
		$this->button_filter       = false;
		$this->button_detail       = false;
		$this->button_action_style = 'button_icon';
		$this->orderby 			   = ['name'=>'asc'];

		
		$this->col   = array();		
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Table","name"=>"table_name");		
		$this->col[] = array("label"=>"Path","name"=>"path"); 		
		$this->col[] = array("label"=>"Controller","name"=>"controller");

		$this->form   = array();	
		$this->form[] = array("label"=>"Name","name"=>"name","placeholder"=>"Module name here",'required'=>true);

		$tables = CRUDBooster::listTables();
		$tables_list = array();		
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = $value;	
				
				if(substr($value, 0,4)=='cms_') continue;

				$tables_list[] = $value."|".$label;
			}
		}
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = "[Default] ".$value;	
				if(substr($value, 0,4)=='cms_') $tables_list[] = $value."|".$label;				
			}
		}		

		$this->form[] = array("label"=>"Table Name","name"=>"table_name","type"=>"select2","dataenum"=>$tables_list,'required'=>true);	

		$fontawesome = array("glass", "music", "search", "envelope-o", "heart", "star", "star-o", "user", "film", "th-large", "th", "th-list", "check", "remove", "close", "times", "search-plus", "search-minus", "power-off", "signal", "gear", "cog", "trash-o", "home", "file-o", "clock-o", "road", "download", "arrow-circle-o-down", "arrow-circle-o-up", "inbox", "play-circle-o", "rotate-right", "repeat", "refresh", "list-alt", "lock", "flag", "headphones", "volume-off", "volume-down", "volume-up", "qrcode", "barcode", "tag", "tags", "book", "bookmark", "print", "camera", "font", "bold", "italic", "text-height", "text-width", "align-left", "align-center", "align-right", "align-justify", "list", "dedent", "outdent", "indent", "video-camera", "photo", "image", "picture-o", "pencil", "map-marker", "adjust", "tint", "edit", "pencil-square-o", "share-square-o", "check-square-o", "arrows", "step-backward", "fast-backward", "backward", "play", "pause", "stop", "forward", "fast-forward", "step-forward", "eject", "chevron-left", "chevron-right", "plus-circle", "minus-circle", "times-circle", "check-circle", "question-circle", "info-circle", "crosshairs", "times-circle-o", "check-circle-o", "ban", "arrow-left", "arrow-right", "arrow-up", "arrow-down", "mail-forward", "share", "expand", "compress", "plus", "minus", "asterisk", "exclamation-circle", "gift", "leaf", "fire", "eye", "eye-slash", "warning", "exclamation-triangle", "plane", "calendar", "random", "comment", "magnet", "chevron-up", "chevron-down", "retweet", "shopping-cart", "folder", "folder-open", "arrows-v", "arrows-h", "bar-chart-o", "bar-chart", "twitter-square", "facebook-square", "camera-retro", "key", "gears", "cogs", "comments", "thumbs-o-up", "thumbs-o-down", "star-half", "heart-o", "sign-out", "linkedin-square", "thumb-tack", "external-link", "sign-in", "trophy", "github-square", "upload", "lemon-o", "phone", "square-o", "bookmark-o", "phone-square", "twitter", "facebook-f", "facebook", "github", "unlock", "credit-card", "feed", "rss", "hdd-o", "bullhorn", "bell", "certificate", "hand-o-right", "hand-o-left", "hand-o-up", "hand-o-down", "arrow-circle-left", "arrow-circle-right", "arrow-circle-up", "arrow-circle-down", "globe", "wrench", "tasks", "filter", "briefcase", "arrows-alt", "group", "users", "chain", "link", "cloud", "flask", "cut", "scissors", "copy", "files-o", "paperclip", "save", "floppy-o", "square", "navicon", "reorder", "bars", "list-ul", "list-ol", "strikethrough", "underline", "table", "magic", "truck", "pinterest", "pinterest-square", "google-plus-square", "google-plus", "money", "caret-down", "caret-up", "caret-left", "caret-right", "columns", "unsorted", "sort", "sort-down", "sort-desc", "sort-up", "sort-asc", "envelope", "linkedin", "rotate-left", "undo", "legal", "gavel", "dashboard", "tachometer", "comment-o", "comments-o", "flash", "bolt", "sitemap", "umbrella", "paste", "clipboard", "lightbulb-o", "exchange", "cloud-download", "cloud-upload", "user-md", "stethoscope", "suitcase", "bell-o", "coffee", "cutlery", "file-text-o", "building-o", "hospital-o", "ambulance", "medkit", "fighter-jet", "beer", "h-square", "plus-square", "angle-double-left", "angle-double-right", "angle-double-up", "angle-double-down", "angle-left", "angle-right", "angle-up", "angle-down", "desktop", "laptop", "tablet", "mobile-phone", "mobile", "circle-o", "quote-left", "quote-right", "spinner", "circle", "mail-reply", "reply", "github-alt", "folder-o", "folder-open-o", "smile-o", "frown-o", "meh-o", "gamepad", "keyboard-o", "flag-o", "flag-checkered", "terminal", "code", "mail-reply-all", "reply-all", "star-half-empty", "star-half-full", "star-half-o", "location-arrow", "crop", "code-fork", "unlink", "chain-broken", "question", "info", "exclamation", "superscript", "subscript", "eraser", "puzzle-piece", "microphone", "microphone-slash", "shield", "calendar-o", "fire-extinguisher", "rocket", "maxcdn", "chevron-circle-left", "chevron-circle-right", "chevron-circle-up", "chevron-circle-down", "html5", "css3", "anchor", "unlock-alt", "bullseye", "ellipsis-h", "ellipsis-v", "rss-square", "play-circle", "ticket", "minus-square", "minus-square-o", "level-up", "level-down", "check-square", "pencil-square", "external-link-square", "share-square", "compass", "toggle-down", "caret-square-o-down", "toggle-up", "caret-square-o-up", "toggle-right", "caret-square-o-right", "euro", "eur", "gbp", "dollar", "usd", "rupee", "inr", "cny", "rmb", "yen", "jpy", "ruble", "rouble", "rub", "won", "krw", "bitcoin", "btc", "file", "file-text", "sort-alpha-asc", "sort-alpha-desc", "sort-amount-asc", "sort-amount-desc", "sort-numeric-asc", "sort-numeric-desc", "thumbs-up", "thumbs-down", "youtube-square", "youtube", "xing", "xing-square", "youtube-play", "dropbox", "stack-overflow", "instagram", "flickr", "adn", "bitbucket", "bitbucket-square", "tumblr", "tumblr-square", "long-arrow-down", "long-arrow-up", "long-arrow-left", "long-arrow-right", "apple", "windows", "android", "linux", "dribbble", "skype", "foursquare", "trello", "female", "male", "gittip", "gratipay", "sun-o", "moon-o", "archive", "bug", "vk", "weibo", "renren", "pagelines", "stack-exchange", "arrow-circle-o-right", "arrow-circle-o-left", "toggle-left", "caret-square-o-left", "dot-circle-o", "wheelchair", "vimeo-square", "turkish-lira", "try", "plus-square-o", "space-shuttle", "slack", "envelope-square", "wordpress", "openid", "institution", "bank", "university", "mortar-board", "graduation-cap", "yahoo", "google", "reddit", "reddit-square", "stumbleupon-circle", "stumbleupon", "delicious", "digg", "pied-piper", "pied-piper-alt", "drupal", "joomla", "language", "fax", "building", "child", "paw", "spoon", "cube", "cubes", "behance", "behance-square", "steam", "steam-square", "recycle", "automobile", "car", "cab", "taxi", "tree", "spotify", "deviantart", "soundcloud", "database", "file-pdf-o", "file-word-o", "file-excel-o", "file-powerpoint-o", "file-photo-o", "file-picture-o", "file-image-o", "file-zip-o", "file-archive-o", "file-sound-o", "file-audio-o", "file-movie-o", "file-video-o", "file-code-o", "vine", "codepen", "jsfiddle", "life-bouy", "life-buoy", "life-saver", "support", "life-ring", "circle-o-notch", "ra", "rebel", "ge", "empire", "git-square", "git", "y-combinator-square", "yc-square", "hacker-news", "tencent-weibo", "qq", "wechat", "weixin", "send", "paper-plane", "send-o", "paper-plane-o", "history", "circle-thin", "header", "paragraph", "sliders", "share-alt", "share-alt-square", "bomb", "soccer-ball-o", "futbol-o", "tty", "binoculars", "plug", "slideshare", "twitch", "yelp", "newspaper-o", "wifi", "calculator", "paypal", "google-wallet", "cc-visa", "cc-mastercard", "cc-discover", "cc-amex", "cc-paypal", "cc-stripe", "bell-slash", "bell-slash-o", "trash", "copyright", "at", "eyedropper", "paint-brush", "birthday-cake", "area-chart", "pie-chart", "line-chart", "lastfm", "lastfm-square", "toggle-off", "toggle-on", "bicycle", "bus", "ioxhost", "angellist", "cc", "shekel", "sheqel", "ils", "meanpath", "buysellads", "connectdevelop", "dashcube", "forumbee", "leanpub", "sellsy", "shirtsinbulk", "simplybuilt", "skyatlas", "cart-plus", "cart-arrow-down", "diamond", "ship", "user-secret", "motorcycle", "street-view", "heartbeat", "venus", "mars", "mercury", "intersex", "transgender", "transgender-alt", "venus-double", "mars-double", "venus-mars", "mars-stroke", "mars-stroke-v", "mars-stroke-h", "neuter", "genderless", "facebook-official", "pinterest-p", "whatsapp", "server", "user-plus", "user-times", "hotel", "bed", "viacoin", "train", "subway", "medium", "yc", "y-combinator", "optin-monster", "opencart", "expeditedssl", "battery-4", "battery-full", "battery-3", "battery-three-quarters", "battery-2", "battery-half", "battery-1", "battery-quarter", "battery-0", "battery-empty", "mouse-pointer", "i-cursor", "object-group", "object-ungroup", "sticky-note", "sticky-note-o", "cc-jcb", "cc-diners-club", "clone", "balance-scale", "hourglass-o", "hourglass-1", "hourglass-start", "hourglass-2", "hourglass-half", "hourglass-3", "hourglass-end", "hourglass", "hand-grab-o", "hand-rock-o", "hand-stop-o", "hand-paper-o", "hand-scissors-o", "hand-lizard-o", "hand-spock-o", "hand-pointer-o", "hand-peace-o", "trademark", "registered", "creative-commons", "gg", "gg-circle", "tripadvisor", "odnoklassniki", "odnoklassniki-square", "get-pocket", "wikipedia-w", "safari", "chrome", "firefox", "opera", "internet-explorer", "tv", "television", "contao", "500px", "amazon", "calendar-plus-o", "calendar-minus-o", "calendar-times-o", "calendar-check-o", "industry", "map-pin", "map-signs", "map-o", "map", "commenting", "commenting-o", "houzz", "vimeo", "black-tie", "fonticons", "reddit-alien", "edge", "credit-card-alt", "codiepie", "modx", "fort-awesome", "usb", "product-hunt", "mixcloud", "scribd", "pause-circle", "pause-circle-o", "stop-circle", "stop-circle-o", "shopping-bag", "shopping-basket", "hashtag", "bluetooth", "bluetooth-b", "percent",);
		$row = CRUDBooster::first($this->table,CRUDBooster::getCurrentId());
		$custom = view('crudbooster::components.list_icon',compact('fontawesome','row'))->render();
		$this->form[] = ['label'=>'Icon','name'=>'icon','type'=>'custom','html'=>$custom,'required'=>true];
				
 		$this->script_js = "
 			$(function() {
 				$('#table_name').change(function() {
					var v = $(this).val();
					$('#path').val(v);
				})	
 			})
 			";

		$this->form[] = array("label"=>"Path","name"=>"path","required"=>true,'placeholder'=>'Optional');
		$this->form[] = array("label"=>"Controller","name"=>"controller","type"=>"text","placeholder"=>"(Optional) Auto Generated");				
						
		
		if(CRUDBooster::getCurrentMethod()=='getAdd' || CRUDBooster::getCurrentMethod()=='postAddSave') {

		$this->form[] = array("label"=>"Global Privilege","name"=>"global_privilege","type"=>"radio","dataenum"=>['0|No','1|Yes'],'value'=>0,'help'=>'Global Privilege allows you to make the module to be accessible by all privileges','exception'=>true);

		$this->form[] = array("label"=>"Button Action Style","name"=>"button_action_style","type"=>"radio","dataenum"=>['button_icon','button_icon_text','button_text','dropdown'],'value'=>'button_icon','exception'=>true);				
		$this->form[] = array("label"=>"Button Table Action","name"=>"button_table_action","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Add","name"=>"button_add","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Delete","name"=>"button_delete","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Edit","name"=>"button_edit","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Detail","name"=>"button_detail","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Show","name"=>"button_show","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Filter","name"=>"button_filter","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'Yes','exception'=>true);				
		$this->form[] = array("label"=>"Button Export","name"=>"button_export","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'No','exception'=>true);				
		$this->form[] = array("label"=>"Button Import","name"=>"button_import","type"=>"radio","dataenum"=>['Yes','No'],'value'=>'No','exception'=>true);						

		}
	
		$this->constructor();
	}	

	function hook_before_delete($id) {
		$modul = DB::table('cms_moduls')->where('id',$id)->first();
		@unlink(app_path('Http/Controllers/'.$modul->controller.'.php'));		
	}
	
	
	public function postAddSave(Request $request) {
		$this->validation($request);					
		$this->input_assignment($request);	

		//Generate Controller 
		$route_basename = basename($request->get('path'));
		if($this->arr['controller']=='') {
			$this->arr['controller'] = CRUDBooster::generateController($request->get('table_name'),$route_basename);			
		}

		$this->arr['created_at'] = date('Y-m-d H:i:s');
		$this->arr['id'] = DB::table($this->table)->max('id') + 1;
		DB::table($this->table)->insert($this->arr);


		//Insert Menu
		if($this->arr['controller']) {
			$parent_menu_sort = DB::table('cms_menus')->where('parent_id',0)->max('sorting')+1;
			$parent_menu_id = DB::table('cms_menus')->max('id')+1;
			DB::table('cms_menus')->insert([
				'id'                =>$parent_menu_id,
				'created_at'        =>date('Y-m-d H:i:s'),
				'name'              =>$this->arr['name'],
				'icon'              =>$this->arr['icon'],
				'path'              =>'#',
				'type'				=>'URL External',
				'is_active'         =>1,
				'id_cms_privileges' =>CRUDBooster::myPrivilegeId(),
				'sorting'           =>$parent_menu_sort,
				'parent_id'         =>0
				]);
			DB::table('cms_menus')->insert([
				'id'                =>DB::table('cms_menus')->max('id')+1,
				'created_at'        =>date('Y-m-d H:i:s'),
				'name'              =>trans("crudbooster.text_default_add_new_module",['module'=>$this->arr['name']]),
				'icon'              =>'fa fa-plus',
				'path' 				=>$this->arr['controller'].'GetAdd',
				'type'				=>'Route',
				'is_active'         =>1,
				'id_cms_privileges' =>CRUDBooster::myPrivilegeId(),
				'sorting'           =>1,
				'parent_id'         =>$parent_menu_id
				]);
			DB::table('cms_menus')->insert([
				'id'                =>DB::table('cms_menus')->max('id')+1,
				'created_at'        =>date('Y-m-d H:i:s'),
				'name'              =>trans("crudbooster.text_default_list_module",['module'=>$this->arr['name']]),
				'icon'              =>'fa fa-bars',
				'path'				=>$this->arr['controller'].'GetIndex',
				'type'				=>'Route',
				'is_active'         =>1,
				'id_cms_privileges' =>CRUDBooster::myPrivilegeId(),
				'sorting'           =>2,
				'parent_id'         =>$parent_menu_id
				]);
		}

		$id_modul = $this->arr['id'];

		$user_id_privileges = CRUDBooster::myPrivilegeId();
		DB::table('cms_privileges_roles')->insert(array(
				'id'                =>DB::table('cms_privileges_roles')->max('id') + 1,
				'id_cms_moduls'     =>$id_modul,
				'id_cms_privileges' =>$user_id_privileges,
				'is_visible'        =>1,
				'is_create'         =>1,
				'is_read'           =>1,
				'is_edit'           =>1,
				'is_delete'         =>1
			));


		//Refresh Session Roles
		$roles = DB::table('cms_privileges_roles')
		->where('id_cms_privileges',CRUDBooster::myPrivilegeId())
		->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
		->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
		->get();
		Session::put('admin_privileges_roles',$roles);


		$ref_parameter = $request->input('ref_parameter');		
		if($request->get('return_url')) {			
			CRUDBooster::redirect($request->get('return_url'),trans("crudbooster.alert_add_data_success"),'success');
		}else{
			if($request->get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
			}
		}
		
	}
	
	
	 
	public function postEditSave(Request $request,$id) {
		
		$this->validation($request);
		$this->input_assignment($request);

		//Generate Controller 
		$route_basename = basename($request->get('path'));
		if($this->arr['controller']=='') {
			$this->arr['controller'] = CRUDBooster::generateController($request->get('table_name'),$route_basename);
		}

		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);


		//Refresh Session Roles
		$roles = DB::table('cms_privileges_roles')
		->where('id_cms_privileges',CRUDBooster::myPrivilegeId())
		->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
		->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
		->get();
		Session::put('admin_privileges_roles',$roles);

		CRUDBooster::redirect($request->server('HTTP_REFERER'),trans('crudbooster.alert_update_data_success'),'success');
		
	}	
	
}
