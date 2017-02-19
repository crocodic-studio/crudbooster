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

	class MenusController extends CBController {

	    public function cbInit() {
	        $this->table              = "cms_menus";
	        $this->primary_key        = "id";
	        $this->title_field        = "name";
	        $this->limit              = 20;
	        $this->orderby      	  = ["id"=>"desc"];
	        
			$this->button_table_action = TRUE;   
			$this->button_action_style = "FALSE";     
			$this->button_add          = FALSE;
			$this->button_delete       = TRUE;
			$this->button_edit         = TRUE;
			$this->button_detail       = TRUE;
			$this->button_show         = FALSE;
			$this->button_filter       = TRUE;        
			$this->button_export       = FALSE;	        
			$this->button_import       = FALSE;	       

			$id = CRUDBooster::getCurrentId();
			$row = CRUDBooster::first($this->table,$id);
			$row = (Request::segment(3) == 'edit')?$row:null;

			$id_module = $id_statistic = 0;

			if($row->type == 'Module') {
				$m = CRUDBooster::first('cms_moduls',['path'=>$row->path]);
				$id_module = $m->id; 
			}elseif ($row->type == 'Statistic') {
				$row->path = str_replace('statistic_builder/show/', '', $row->path);
				$m = CRUDBooster::first('cms_statistics',['slug'=>$row->path]);
				$id_statistic = $m->id;
			}			

			$this->script_js = "
			$(function() {
				var current_id = '$id';
				var current_type = '$row->type';
				var type_menu = $('input[name=type]').val();
				type_menu = (current_type)?current_type:type_menu;
				if(type_menu == 'Module') {
					$('#form-group-module_slug').show();
					$('#form-group-statistic_slug,#form-group-path').hide();
					$('#module_slug').prop('required',true);
					$('#form-group-module_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
				}else if(type_menu == 'Statistic') {
					$('#form-group-statistic_slug').show();
					$('#form-group-module_slug,#form-group-path').hide();
					$('#statistic_slug').prop('required',true);
					$('#form-group-statistic_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
				}else{
					$('#form-group-module_slug,#form-group-statistic_slug').hide();
					$('#form-group-path').show();
				}

				$('input[name=type]').click(function() {
					var n = $(this).val();
					var isCheck = $(this).prop('checked');
					if(n == 'Module') {
						$('#form-group-path').hide();
						$('#form-group-statistic_slug').hide();
						$('#statistic_slug,#path').prop('required',false);

						$('#form-group-module_slug').show();
						$('#module_slug').prop('required',true);
						$('#form-group-module_slug label .text-danger').remove();
						$('#form-group-module_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
					}else if (n == 'Statistic') {
						$('#form-group-path').hide();
						$('#form-group-module_slug').hide();
						$('#module_slug,#path').prop('required',false);

						$('#form-group-statistic_slug').show();
						$('#statistic_slug').prop('required',true);
						$('#form-group-statistic_slug label .text-danger').remove();
						$('#form-group-statistic_slug label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');
					}else {
						$('#module_slug,#statistic_slug').prop('required',false);
						
						$('#path').prop('required',true);
						$('#form-group-path label .text-danger').remove();
						$('#form-group-path label').append('<span class=\"text-danger\" title=\"This field is required\">*</span>');

						$('#form-group-path').show();
						$('#form-group-module_slug,#form-group-statistic_slug').hide();
					}
				})
			})
			";

			$this->col = array();
			$this->col[] = array("label"=>"Name","name"=>"name" );
			$this->col[] = array("label"=>"Is Active","name"=>"is_active" );
			$this->col[] = array("label"=>"Privileges","name"=>"id_cms_privileges","join"=>"cms_privileges,name");

			$this->form   = array();
			$this->form[] = array("label"=>"Name","name"=>"name","type"=>"text","required"=>TRUE,"validation"=>"required|min:3|max:255|alpha_spaces","placeholder"=>"You can only enter the letter only");
			$this->form[] = array("label"=>"Type","name"=>"type","type"=>"radio","required"=>TRUE,'dataenum'=>['Module','Statistic','URL','Controller & Method','Route'],'value'=>'Module');			

			$this->form[] = array("label"=>"Module","name"=>"module_slug","type"=>"select","datatable"=>"cms_moduls,name","datatable_where"=>"is_protected = 0","value"=>$id_module);
			$this->form[] = array("label"=>"Statistic","name"=>"statistic_slug","type"=>"select","datatable"=>"cms_statistics,name","style"=>"display:none","value"=>$id_statistic);

			$this->form[] = array("label"=>"Value","name"=>"path","type"=>"text",'help'=>'If you select type controller, you can fill this field with controller name, you may include the method also','placeholder'=>'NameController or NameController@methodName',"style"=>"display:none");

			$fontawesome  = array("glass", "music", "search", "envelope-o", "heart", "star", "star-o", "user", "film", "th-large", "th", "th-list", "check", "remove", "close", "times", "search-plus", "search-minus", "power-off", "signal", "gear", "cog", "trash-o", "home", "file-o", "clock-o", "road", "download", "arrow-circle-o-down", "arrow-circle-o-up", "inbox", "play-circle-o", "rotate-right", "repeat", "refresh", "list-alt", "lock", "flag", "headphones", "volume-off", "volume-down", "volume-up", "qrcode", "barcode", "tag", "tags", "book", "bookmark", "print", "camera", "font", "bold", "italic", "text-height", "text-width", "align-left", "align-center", "align-right", "align-justify", "list", "dedent", "outdent", "indent", "video-camera", "photo", "image", "picture-o", "pencil", "map-marker", "adjust", "tint", "edit", "pencil-square-o", "share-square-o", "check-square-o", "arrows", "step-backward", "fast-backward", "backward", "play", "pause", "stop", "forward", "fast-forward", "step-forward", "eject", "chevron-left", "chevron-right", "plus-circle", "minus-circle", "times-circle", "check-circle", "question-circle", "info-circle", "crosshairs", "times-circle-o", "check-circle-o", "ban", "arrow-left", "arrow-right", "arrow-up", "arrow-down", "mail-forward", "share", "expand", "compress", "plus", "minus", "asterisk", "exclamation-circle", "gift", "leaf", "fire", "eye", "eye-slash", "warning", "exclamation-triangle", "plane", "calendar", "random", "comment", "magnet", "chevron-up", "chevron-down", "retweet", "shopping-cart", "folder", "folder-open", "arrows-v", "arrows-h", "bar-chart-o", "bar-chart", "twitter-square", "facebook-square", "camera-retro", "key", "gears", "cogs", "comments", "thumbs-o-up", "thumbs-o-down", "star-half", "heart-o", "sign-out", "linkedin-square", "thumb-tack", "external-link", "sign-in", "trophy", "github-square", "upload", "lemon-o", "phone", "square-o", "bookmark-o", "phone-square", "twitter", "facebook-f", "facebook", "github", "unlock", "credit-card", "feed", "rss", "hdd-o", "bullhorn", "bell", "certificate", "hand-o-right", "hand-o-left", "hand-o-up", "hand-o-down", "arrow-circle-left", "arrow-circle-right", "arrow-circle-up", "arrow-circle-down", "globe", "wrench", "tasks", "filter", "briefcase", "arrows-alt", "group", "users", "chain", "link", "cloud", "flask", "cut", "scissors", "copy", "files-o", "paperclip", "save", "floppy-o", "square", "navicon", "reorder", "bars", "list-ul", "list-ol", "strikethrough", "underline", "table", "magic", "truck", "pinterest", "pinterest-square", "google-plus-square", "google-plus", "money", "caret-down", "caret-up", "caret-left", "caret-right", "columns", "unsorted", "sort", "sort-down", "sort-desc", "sort-up", "sort-asc", "envelope", "linkedin", "rotate-left", "undo", "legal", "gavel", "dashboard", "tachometer", "comment-o", "comments-o", "flash", "bolt", "sitemap", "umbrella", "paste", "clipboard", "lightbulb-o", "exchange", "cloud-download", "cloud-upload", "user-md", "stethoscope", "suitcase", "bell-o", "coffee", "cutlery", "file-text-o", "building-o", "hospital-o", "ambulance", "medkit", "fighter-jet", "beer", "h-square", "plus-square", "angle-double-left", "angle-double-right", "angle-double-up", "angle-double-down", "angle-left", "angle-right", "angle-up", "angle-down", "desktop", "laptop", "tablet", "mobile-phone", "mobile", "circle-o", "quote-left", "quote-right", "spinner", "circle", "mail-reply", "reply", "github-alt", "folder-o", "folder-open-o", "smile-o", "frown-o", "meh-o", "gamepad", "keyboard-o", "flag-o", "flag-checkered", "terminal", "code", "mail-reply-all", "reply-all", "star-half-empty", "star-half-full", "star-half-o", "location-arrow", "crop", "code-fork", "unlink", "chain-broken", "question", "info", "exclamation", "superscript", "subscript", "eraser", "puzzle-piece", "microphone", "microphone-slash", "shield", "calendar-o", "fire-extinguisher", "rocket", "maxcdn", "chevron-circle-left", "chevron-circle-right", "chevron-circle-up", "chevron-circle-down", "html5", "css3", "anchor", "unlock-alt", "bullseye", "ellipsis-h", "ellipsis-v", "rss-square", "play-circle", "ticket", "minus-square", "minus-square-o", "level-up", "level-down", "check-square", "pencil-square", "external-link-square", "share-square", "compass", "toggle-down", "caret-square-o-down", "toggle-up", "caret-square-o-up", "toggle-right", "caret-square-o-right", "euro", "eur", "gbp", "dollar", "usd", "rupee", "inr", "cny", "rmb", "yen", "jpy", "ruble", "rouble", "rub", "won", "krw", "bitcoin", "btc", "file", "file-text", "sort-alpha-asc", "sort-alpha-desc", "sort-amount-asc", "sort-amount-desc", "sort-numeric-asc", "sort-numeric-desc", "thumbs-up", "thumbs-down", "youtube-square", "youtube", "xing", "xing-square", "youtube-play", "dropbox", "stack-overflow", "instagram", "flickr", "adn", "bitbucket", "bitbucket-square", "tumblr", "tumblr-square", "long-arrow-down", "long-arrow-up", "long-arrow-left", "long-arrow-right", "apple", "windows", "android", "linux", "dribbble", "skype", "foursquare", "trello", "female", "male", "gittip", "gratipay", "sun-o", "moon-o", "archive", "bug", "vk", "weibo", "renren", "pagelines", "stack-exchange", "arrow-circle-o-right", "arrow-circle-o-left", "toggle-left", "caret-square-o-left", "dot-circle-o", "wheelchair", "vimeo-square", "turkish-lira", "try", "plus-square-o", "space-shuttle", "slack", "envelope-square", "wordpress", "openid", "institution", "bank", "university", "mortar-board", "graduation-cap", "yahoo", "google", "reddit", "reddit-square", "stumbleupon-circle", "stumbleupon", "delicious", "digg", "pied-piper", "pied-piper-alt", "drupal", "joomla", "language", "fax", "building", "child", "paw", "spoon", "cube", "cubes", "behance", "behance-square", "steam", "steam-square", "recycle", "automobile", "car", "cab", "taxi", "tree", "spotify", "deviantart", "soundcloud", "database", "file-pdf-o", "file-word-o", "file-excel-o", "file-powerpoint-o", "file-photo-o", "file-picture-o", "file-image-o", "file-zip-o", "file-archive-o", "file-sound-o", "file-audio-o", "file-movie-o", "file-video-o", "file-code-o", "vine", "codepen", "jsfiddle", "life-bouy", "life-buoy", "life-saver", "support", "life-ring", "circle-o-notch", "ra", "rebel", "ge", "empire", "git-square", "git", "y-combinator-square", "yc-square", "hacker-news", "tencent-weibo", "qq", "wechat", "weixin", "send", "paper-plane", "send-o", "paper-plane-o", "history", "circle-thin", "header", "paragraph", "sliders", "share-alt", "share-alt-square", "bomb", "soccer-ball-o", "futbol-o", "tty", "binoculars", "plug", "slideshare", "twitch", "yelp", "newspaper-o", "wifi", "calculator", "paypal", "google-wallet", "cc-visa", "cc-mastercard", "cc-discover", "cc-amex", "cc-paypal", "cc-stripe", "bell-slash", "bell-slash-o", "trash", "copyright", "at", "eyedropper", "paint-brush", "birthday-cake", "area-chart", "pie-chart", "line-chart", "lastfm", "lastfm-square", "toggle-off", "toggle-on", "bicycle", "bus", "ioxhost", "angellist", "cc", "shekel", "sheqel", "ils", "meanpath", "buysellads", "connectdevelop", "dashcube", "forumbee", "leanpub", "sellsy", "shirtsinbulk", "simplybuilt", "skyatlas", "cart-plus", "cart-arrow-down", "diamond", "ship", "user-secret", "motorcycle", "street-view", "heartbeat", "venus", "mars", "mercury", "intersex", "transgender", "transgender-alt", "venus-double", "mars-double", "venus-mars", "mars-stroke", "mars-stroke-v", "mars-stroke-h", "neuter", "genderless", "facebook-official", "pinterest-p", "whatsapp", "server", "user-plus", "user-times", "hotel", "bed", "viacoin", "train", "subway", "medium", "yc", "y-combinator", "optin-monster", "opencart", "expeditedssl", "battery-4", "battery-full", "battery-3", "battery-three-quarters", "battery-2", "battery-half", "battery-1", "battery-quarter", "battery-0", "battery-empty", "mouse-pointer", "i-cursor", "object-group", "object-ungroup", "sticky-note", "sticky-note-o", "cc-jcb", "cc-diners-club", "clone", "balance-scale", "hourglass-o", "hourglass-1", "hourglass-start", "hourglass-2", "hourglass-half", "hourglass-3", "hourglass-end", "hourglass", "hand-grab-o", "hand-rock-o", "hand-stop-o", "hand-paper-o", "hand-scissors-o", "hand-lizard-o", "hand-spock-o", "hand-pointer-o", "hand-peace-o", "trademark", "registered", "creative-commons", "gg", "gg-circle", "tripadvisor", "odnoklassniki", "odnoklassniki-square", "get-pocket", "wikipedia-w", "safari", "chrome", "firefox", "opera", "internet-explorer", "tv", "television", "contao", "500px", "amazon", "calendar-plus-o", "calendar-minus-o", "calendar-times-o", "calendar-check-o", "industry", "map-pin", "map-signs", "map-o", "map", "commenting", "commenting-o", "houzz", "vimeo", "black-tie", "fonticons", "reddit-alien", "edge", "credit-card-alt", "codiepie", "modx", "fort-awesome", "usb", "product-hunt", "mixcloud", "scribd", "pause-circle", "pause-circle-o", "stop-circle", "stop-circle-o", "shopping-bag", "shopping-basket", "hashtag", "bluetooth", "bluetooth-b", "percent",);
			
			$custom       = view('crudbooster::components.list_icon',compact('fontawesome','row'))->render();
			$this->form[] = ['label'=>'Icon','name'=>'icon','type'=>'custom','html'=>$custom,'required'=>true];
			$this->form[] = ['label'=>'Color','name'=>'color','type'=>'select2','dataenum'=>['normal','red','green','aqua','light-blue','red','yellow','muted'],'required'=>true,'value'=>'normal'];
			$this->form[] = array("label"=>"Active","name"=>"is_active","type"=>"radio","required"=>TRUE,"validation"=>"required|integer","dataenum"=>array('1|Active','0|InActive'),'value'=>'1');
			$this->form[] = array("label"=>"Dashboard","name"=>"is_dashboard","type"=>"radio","required"=>TRUE,"validation"=>"required|integer","dataenum"=>array('1|Yes','0|No'),'value'=>'0');

			$id_cms_privileges = Request::get('id_cms_privileges');
			$this->form[] = array("label"=>"id_cms_privileges","name"=>"id_cms_privileges","type"=>"hidden","value"=>$id_cms_privileges);
    	        
	    }


	  public function getIndex() {
	  	$this->cbLoader();

	  	$module = CRUDBooster::getCurrentModule();
	  	if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}
	  	
	  	$privileges = DB::table('cms_privileges')->get();

	  	$id_cms_privileges = Request::get('id_cms_privileges');
	  	$id_cms_privileges = ($id_cms_privileges)?:CRUDBooster::myPrivilegeId();


	  	$menu_active = DB::table('cms_menus')
	  	->where('id_cms_privileges',$id_cms_privileges)
	  	->where('parent_id',0)
	  	->where('is_active',1)
	  	->orderby('sorting','asc')
	  	->get();

	  	foreach($menu_active as &$menu) {
	  		$child = DB::table('cms_menus')->where('is_active',1)->where('parent_id',$menu->id)->orderby('sorting','asc')->get();
	  		if(count($child)) {
	  			$menu->children = $child;
	  		}
	  	}

	  	$menu_inactive = DB::table('cms_menus')
	  	->where('id_cms_privileges',$id_cms_privileges)
	  	->where('parent_id',0)
	  	->where('is_active',0)
	  	->orderby('sorting','asc')
	  	->get();

	  	foreach($menu_inactive as &$menu) {
	  		$child = DB::table('cms_menus')->where('is_active',1)->where('parent_id',$menu->id)->orderby('sorting','asc')->get();
	  		if(count($child)) {
	  			$menu->children = $child;
	  		}
	  	}

	  	$return_url = Request::fullUrl();

	  	$page_title = 'Menu Management';

	  	return view('crudbooster::menus_management',compact('menu_active','menu_inactive','privileges','id_cms_privileges','return_url','page_title'));
	  }

	  public function hook_before_add(&$postdata) {
	  		if(!$postdata['id_cms_privileges']) {
	  			$postdata['id_cms_privileges'] = CRUDBooster::myPrivilegeId();
	  		}
	  		$postdata['parent_id'] = 0;

	  		if($postdata['type'] == 'Statistic') {
	  			$stat = CRUDBooster::first('cms_statistics',['id'=>$postdata['statistic_slug']]);
	  			$postdata['path'] = 'statistic_builder/show/'.$stat->slug;	  			
	  		}elseif ($postdata['type'] == 'Module') {
	  			$stat = CRUDBooster::first('cms_moduls',['id'=>$postdata['module_slug']]);
	  			$postdata['path'] = $stat->path;	  		
	  		}

	  		unset($postdata['module_slug']);
	  		unset($postdata['statistic_slug']);

	  		if($postdata['is_dashboard']==1) {
	  			//If set dashboard, so unset for first all dashboard
	  			DB::table('cms_menus')->where('id_cms_privileges',$postdata['id_cms_privileges'])->where('is_dashboard',1)->update(['is_dashboard'=>0]);
	  			Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());	  			
	  		}
	  }

	  public function hook_before_edit(&$postdata,$id) {

	  	if($postdata['is_dashboard']==1) {
  			//If set dashboard, so unset for first all dashboard
  			DB::table('cms_menus')
  			->where('id_cms_privileges',$postdata['id_cms_privileges'])
  			->where('is_dashboard',1)->update(['is_dashboard'=>0]);
  			Cache::forget('sidebarDashboard'.CRUDBooster::myPrivilegeId());
  		}

  		if($postdata['type'] == 'Statistic') {
  			$stat = CRUDBooster::first('cms_statistics',['id'=>$postdata['statistic_slug']]);  			
  			$postdata['path'] = 'statistic_builder/show/'.$stat->slug;	  			
  		}elseif ($postdata['type'] == 'Module') {
  			$stat = CRUDBooster::first('cms_moduls',['id'=>$postdata['module_slug']]);  			
  			$postdata['path'] = $stat->path;	  		
  		}

  		unset($postdata['module_slug']);
  		unset($postdata['statistic_slug']);
	  }

	  public function hook_after_delete($id) {
	  	DB::table('cms_menus')->where('parent_id',$id)->delete();
	  }

	  public function postSaveMenu() {
	  		$post = Request::input('menus');
	  		$isActive = Request::input('isActive');
	  		$post = json_decode($post,true);	  		
	  		
	  		$i = 1;
	  		foreach($post[0] as $ro) {
	  			$pid = $ro['id'];
	  			if($ro['children'][0]) {
	  				$ci = 1;
	  				foreach($ro['children'][0] as $c) {
	  					$id = $c['id'];
	  					DB::table('cms_menus')->where('id',$id)->update(['sorting'=>$ci,'parent_id'=>$pid,'is_active'=>$isActive]);
	  					$ci++;
	  				}
	  			}
	  			DB::table('cms_menus')->where('id',$pid)->update(['sorting'=>$i,'parent_id'=>0,'is_active'=>$isActive]);
	  			$i++;
	  		}

	  		return response()->json(['success'=>true]);
	  }

	}