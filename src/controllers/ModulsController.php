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

class ModulsController extends CBController {
	
	public function cbInit() {		
		$this->table               = 'cms_moduls';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->limit               = 100;
		$this->button_add 		   = false;
		$this->button_export       = false;
		$this->button_import       = false;
		$this->button_filter       = false;
		$this->button_detail       = false;
		$this->button_action_style = 'button_icon';
		$this->orderby 			   = ['is_protected'=>'asc','name'=>'asc'];

		
		$this->col   = array();		
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Table","name"=>"table_name");		
		$this->col[] = array("label"=>"Path","name"=>"path"); 		
		$this->col[] = array("label"=>"Controller","name"=>"controller");
		$this->col[] = array("label"=>"Protected","name"=>"is_protected","visible"=>false);

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

		$this->addaction[] = ['label'=>'Module Wizard','icon'=>'fa fa-wrench','url'=>CRUDBooster::mainpath('step1').'/[id]',"showIf"=>"[is_protected] == 0"];
	
		$this->index_button[] = ['label'=>'Generate New Module','icon'=>'fa fa-plus','url'=>CRUDBooster::mainpath('step1'),'color'=>'success'];
		
	}	

	function hook_query_index(&$query) {
		$query->where('is_protected',0);	
	}

	function hook_before_delete($id) {
		$modul = DB::table('cms_moduls')->where('id',$id)->first();				
		$menus = DB::table('cms_menus')->where('path','like','%'.$modul->controller.'%')->delete();		
		@unlink(app_path('Http/Controllers/'.$modul->controller.'.php'));		
	}

	public function getTableColumns($table) {
		$columns = CRUDBooster::getTableColumns($table);
		return response()->json($columns);
	}

	public function getCheckSlug($slug) {
		$check = DB::table('cms_moduls')->where('path',$slug)->count();
		$lastId = DB::table('cms_moduls')->max('id')+1;
		return response()->json(['total'=>$check,'lastid'=>$lastId]);
	}

	public function getAdd() {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		return redirect()->route("ModulsControllerGetStep1");
	}

	public function getStep1($id=0) {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$tables = CRUDBooster::listTables();
		$tables_list = array();		
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = $value;	

				if(substr($label, 0,4) == 'cms_' && $label != 'cms_users') continue;
				if($label == 'migrations') continue;

				$tables_list[] = $value;
			}
		}		

		$fontawesome = array("glass", "music", "search", "envelope-o", "heart", "star", "star-o", "user", "film", "th-large", "th", "th-list", "check", "remove", "close", "times", "search-plus", "search-minus", "power-off", "signal", "gear", "cog", "trash-o", "home", "file-o", "clock-o", "road", "download", "arrow-circle-o-down", "arrow-circle-o-up", "inbox", "play-circle-o", "rotate-right", "repeat", "refresh", "list-alt", "lock", "flag", "headphones", "volume-off", "volume-down", "volume-up", "qrcode", "barcode", "tag", "tags", "book", "bookmark", "print", "camera", "font", "bold", "italic", "text-height", "text-width", "align-left", "align-center", "align-right", "align-justify", "list", "dedent", "outdent", "indent", "video-camera", "photo", "image", "picture-o", "pencil", "map-marker", "adjust", "tint", "edit", "pencil-square-o", "share-square-o", "check-square-o", "arrows", "step-backward", "fast-backward", "backward", "play", "pause", "stop", "forward", "fast-forward", "step-forward", "eject", "chevron-left", "chevron-right", "plus-circle", "minus-circle", "times-circle", "check-circle", "question-circle", "info-circle", "crosshairs", "times-circle-o", "check-circle-o", "ban", "arrow-left", "arrow-right", "arrow-up", "arrow-down", "mail-forward", "share", "expand", "compress", "plus", "minus", "asterisk", "exclamation-circle", "gift", "leaf", "fire", "eye", "eye-slash", "warning", "exclamation-triangle", "plane", "calendar", "random", "comment", "magnet", "chevron-up", "chevron-down", "retweet", "shopping-cart", "folder", "folder-open", "arrows-v", "arrows-h", "bar-chart-o", "bar-chart", "twitter-square", "facebook-square", "camera-retro", "key", "gears", "cogs", "comments", "thumbs-o-up", "thumbs-o-down", "star-half", "heart-o", "sign-out", "linkedin-square", "thumb-tack", "external-link", "sign-in", "trophy", "github-square", "upload", "lemon-o", "phone", "square-o", "bookmark-o", "phone-square", "twitter", "facebook-f", "facebook", "github", "unlock", "credit-card", "feed", "rss", "hdd-o", "bullhorn", "bell", "certificate", "hand-o-right", "hand-o-left", "hand-o-up", "hand-o-down", "arrow-circle-left", "arrow-circle-right", "arrow-circle-up", "arrow-circle-down", "globe", "wrench", "tasks", "filter", "briefcase", "arrows-alt", "group", "users", "chain", "link", "cloud", "flask", "cut", "scissors", "copy", "files-o", "paperclip", "save", "floppy-o", "square", "navicon", "reorder", "bars", "list-ul", "list-ol", "strikethrough", "underline", "table", "magic", "truck", "pinterest", "pinterest-square", "google-plus-square", "google-plus", "money", "caret-down", "caret-up", "caret-left", "caret-right", "columns", "unsorted", "sort", "sort-down", "sort-desc", "sort-up", "sort-asc", "envelope", "linkedin", "rotate-left", "undo", "legal", "gavel", "dashboard", "tachometer", "comment-o", "comments-o", "flash", "bolt", "sitemap", "umbrella", "paste", "clipboard", "lightbulb-o", "exchange", "cloud-download", "cloud-upload", "user-md", "stethoscope", "suitcase", "bell-o", "coffee", "cutlery", "file-text-o", "building-o", "hospital-o", "ambulance", "medkit", "fighter-jet", "beer", "h-square", "plus-square", "angle-double-left", "angle-double-right", "angle-double-up", "angle-double-down", "angle-left", "angle-right", "angle-up", "angle-down", "desktop", "laptop", "tablet", "mobile-phone", "mobile", "circle-o", "quote-left", "quote-right", "spinner", "circle", "mail-reply", "reply", "github-alt", "folder-o", "folder-open-o", "smile-o", "frown-o", "meh-o", "gamepad", "keyboard-o", "flag-o", "flag-checkered", "terminal", "code", "mail-reply-all", "reply-all", "star-half-empty", "star-half-full", "star-half-o", "location-arrow", "crop", "code-fork", "unlink", "chain-broken", "question", "info", "exclamation", "superscript", "subscript", "eraser", "puzzle-piece", "microphone", "microphone-slash", "shield", "calendar-o", "fire-extinguisher", "rocket", "maxcdn", "chevron-circle-left", "chevron-circle-right", "chevron-circle-up", "chevron-circle-down", "html5", "css3", "anchor", "unlock-alt", "bullseye", "ellipsis-h", "ellipsis-v", "rss-square", "play-circle", "ticket", "minus-square", "minus-square-o", "level-up", "level-down", "check-square", "pencil-square", "external-link-square", "share-square", "compass", "toggle-down", "caret-square-o-down", "toggle-up", "caret-square-o-up", "toggle-right", "caret-square-o-right", "euro", "eur", "gbp", "dollar", "usd", "rupee", "inr", "cny", "rmb", "yen", "jpy", "ruble", "rouble", "rub", "won", "krw", "bitcoin", "btc", "file", "file-text", "sort-alpha-asc", "sort-alpha-desc", "sort-amount-asc", "sort-amount-desc", "sort-numeric-asc", "sort-numeric-desc", "thumbs-up", "thumbs-down", "youtube-square", "youtube", "xing", "xing-square", "youtube-play", "dropbox", "stack-overflow", "instagram", "flickr", "adn", "bitbucket", "bitbucket-square", "tumblr", "tumblr-square", "long-arrow-down", "long-arrow-up", "long-arrow-left", "long-arrow-right", "apple", "windows", "android", "linux", "dribbble", "skype", "foursquare", "trello", "female", "male", "gittip", "gratipay", "sun-o", "moon-o", "archive", "bug", "vk", "weibo", "renren", "pagelines", "stack-exchange", "arrow-circle-o-right", "arrow-circle-o-left", "toggle-left", "caret-square-o-left", "dot-circle-o", "wheelchair", "vimeo-square", "turkish-lira", "try", "plus-square-o", "space-shuttle", "slack", "envelope-square", "wordpress", "openid", "institution", "bank", "university", "mortar-board", "graduation-cap", "yahoo", "google", "reddit", "reddit-square", "stumbleupon-circle", "stumbleupon", "delicious", "digg", "pied-piper", "pied-piper-alt", "drupal", "joomla", "language", "fax", "building", "child", "paw", "spoon", "cube", "cubes", "behance", "behance-square", "steam", "steam-square", "recycle", "automobile", "car", "cab", "taxi", "tree", "spotify", "deviantart", "soundcloud", "database", "file-pdf-o", "file-word-o", "file-excel-o", "file-powerpoint-o", "file-photo-o", "file-picture-o", "file-image-o", "file-zip-o", "file-archive-o", "file-sound-o", "file-audio-o", "file-movie-o", "file-video-o", "file-code-o", "vine", "codepen", "jsfiddle", "life-bouy", "life-buoy", "life-saver", "support", "life-ring", "circle-o-notch", "ra", "rebel", "ge", "empire", "git-square", "git", "y-combinator-square", "yc-square", "hacker-news", "tencent-weibo", "qq", "wechat", "weixin", "send", "paper-plane", "send-o", "paper-plane-o", "history", "circle-thin", "header", "paragraph", "sliders", "share-alt", "share-alt-square", "bomb", "soccer-ball-o", "futbol-o", "tty", "binoculars", "plug", "slideshare", "twitch", "yelp", "newspaper-o", "wifi", "calculator", "paypal", "google-wallet", "cc-visa", "cc-mastercard", "cc-discover", "cc-amex", "cc-paypal", "cc-stripe", "bell-slash", "bell-slash-o", "trash", "copyright", "at", "eyedropper", "paint-brush", "birthday-cake", "area-chart", "pie-chart", "line-chart", "lastfm", "lastfm-square", "toggle-off", "toggle-on", "bicycle", "bus", "ioxhost", "angellist", "cc", "shekel", "sheqel", "ils", "meanpath", "buysellads", "connectdevelop", "dashcube", "forumbee", "leanpub", "sellsy", "shirtsinbulk", "simplybuilt", "skyatlas", "cart-plus", "cart-arrow-down", "diamond", "ship", "user-secret", "motorcycle", "street-view", "heartbeat", "venus", "mars", "mercury", "intersex", "transgender", "transgender-alt", "venus-double", "mars-double", "venus-mars", "mars-stroke", "mars-stroke-v", "mars-stroke-h", "neuter", "genderless", "facebook-official", "pinterest-p", "whatsapp", "server", "user-plus", "user-times", "hotel", "bed", "viacoin", "train", "subway", "medium", "yc", "y-combinator", "optin-monster", "opencart", "expeditedssl", "battery-4", "battery-full", "battery-3", "battery-three-quarters", "battery-2", "battery-half", "battery-1", "battery-quarter", "battery-0", "battery-empty", "mouse-pointer", "i-cursor", "object-group", "object-ungroup", "sticky-note", "sticky-note-o", "cc-jcb", "cc-diners-club", "clone", "balance-scale", "hourglass-o", "hourglass-1", "hourglass-start", "hourglass-2", "hourglass-half", "hourglass-3", "hourglass-end", "hourglass", "hand-grab-o", "hand-rock-o", "hand-stop-o", "hand-paper-o", "hand-scissors-o", "hand-lizard-o", "hand-spock-o", "hand-pointer-o", "hand-peace-o", "trademark", "registered", "creative-commons", "gg", "gg-circle", "tripadvisor", "odnoklassniki", "odnoklassniki-square", "get-pocket", "wikipedia-w", "safari", "chrome", "firefox", "opera", "internet-explorer", "tv", "television", "contao", "500px", "amazon", "calendar-plus-o", "calendar-minus-o", "calendar-times-o", "calendar-check-o", "industry", "map-pin", "map-signs", "map-o", "map", "commenting", "commenting-o", "houzz", "vimeo", "black-tie", "fonticons", "reddit-alien", "edge", "credit-card-alt", "codiepie", "modx", "fort-awesome", "usb", "product-hunt", "mixcloud", "scribd", "pause-circle", "pause-circle-o", "stop-circle", "stop-circle-o", "shopping-bag", "shopping-basket", "hashtag", "bluetooth", "bluetooth-b", "percent");

		$row = CRUDBooster::first($this->table,['id'=>$id]);

		return view("crudbooster::module_generator.step1",compact("tables_list","fontawesome","row","id"));
	}

	public function getStep2($id) {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$row = DB::table('cms_moduls')->where('id',$id)->first();

		$columns = CRUDBooster::getTableColumns($row->table_name);

		$tables = CRUDBooster::listTables();
		$table_list = array();		
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {	
				$label = $value;									
				$table_list[] = $value;
			}
		}

		if(file_exists(app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php'))) {
			$response = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
			$column_datas = extract_unit($response,"# START COLUMNS DO NOT REMOVE THIS LINE","# END COLUMNS DO NOT REMOVE THIS LINE");			
			$column_datas = str_replace('$this->','$cb_', $column_datas);			
			eval($column_datas);
		}

		$data                              = [];
		$data['id']                        = $id;
		$data['columns']                   = $columns;
		$data['table_list']                = $table_list;
		$data['cb_col']                    = $cb_col;		


		return view('crudbooster::module_generator.step2',$data);
	}

	public function postStep2() {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$name       = Request::get('name');
		$table_name = Request::get('table');
		$icon       = Request::get('icon');
		$path       = Request::get('path');

		if(!Request::get('id')) {

			if(DB::table('cms_moduls')->where('path',$path)->count()) {
				return redirect()->back()->with(['message'=>'Sorry the slug has already exists, please choose another !','message_type'=>'warning']);
			}			


			$created_at = now();
			$id = DB::table($this->table)->max('id') + 1;

			$controller = CRUDBooster::generateController($table_name,$path);
			DB::table($this->table)->insert(compact("controller","name","table_name","icon","path","created_at","id"));

			//Insert Menu
			if($controller && Request::get('create_menu')) {
				$parent_menu_sort = DB::table('cms_menus')->where('parent_id',0)->max('sorting')+1;
				$parent_menu_id = DB::table('cms_menus')->max('id')+1;

				DB::table('cms_menus')->insert([
					'id'                =>$parent_menu_id,
					'created_at'        =>date('Y-m-d H:i:s'),
					'name'              =>$name,
					'icon'              =>$icon,
					'path'				=>$controller.'GetIndex',
					'type'				=>'Route',
					'is_active'         =>1,
					'id_cms_privileges' =>CRUDBooster::myPrivilegeId(),
					'sorting'           =>$parent_menu_sort,
					'parent_id'         =>0
					]);
			}			

			$user_id_privileges = CRUDBooster::myPrivilegeId();
			DB::table('cms_privileges_roles')->insert(array(
					'id'                =>DB::table('cms_privileges_roles')->max('id') + 1,
					'id_cms_moduls'     =>$id,
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
			return redirect(Route("ModulsControllerGetStep2",["id"=>$id]));
		}else{
			$id = Request::get('id');
			DB::table($this->table)->where('id',$id)->update(compact("name","table_name","icon","path"));	

			$row = DB::table('cms_moduls')->where('id',$id)->first();
			
			if(file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
				$response = file_get_contents(app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php'));	
			}else{
				$response = file_get_contents(__DIR__.'/'.str_replace('.', '', $row->controller).'.php');	
			}			

			if(strpos($response, "# START COLUMNS") !== TRUE) {
				// return redirect()->back()->with(['message'=>'Sorry, is not possible to edit the module with Module Generator Tool. Prefix and or Suffix tag is missing !','message_type'=>'warning']);	
			}

			
			return redirect(Route("ModulsControllerGetStep2",["id"=>$id]));		
		}
	}

	public function postStep3() {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$column      = Request::input('column');
		$name        = Request::input('name');
		$join_table  = Request::input('join_table');
		$join_field  = Request::input('join_field');
		$is_image    = Request::input('is_image');
		$is_download = Request::input('is_download');
		$callbackphp = Request::input('callbackphp');
		$id          = Request::input('id');		
		$width       = Request::input('width');

		$row = DB::table('cms_moduls')->where('id',$id)->first();	

		$i             = 0;
		$script_cols   = [];		
		foreach($column as $col) {

			if(!$name[$i]) {
				$i++;
				continue;
			}

			$script_cols[$i] = "\t\t\t".'$this->col[] = ["label"=>"'.$col.'","name"=>"'.$name[$i].'"';
			
			if($join_table[$i] && $join_field[$i]) {
				$script_cols[$i] .= ',"join"=>"'.$join_table[$i].','.$join_field[$i].'"';
			}

			if($is_image[$i]) {
				$script_cols[$i] .= ',"image"=>true';
			}

			if($id_download[$i]) {
				$script_cols[$i] .= ',"download"=>true';
			}

			if($width[$i]) {
				$script_cols[$i] .= ',"width"=>"'.$width[$i].'"';
			}

			if($callbackphp[$i]) {
				$script_cols[$i] .= ',"callback_php"=>\''.$callbackphp[$i].'\'';
			}

			$script_cols[$i] .= "];";
			
			$i++;
		}		

		$scripts    = implode("\n",$script_cols);
		$raw        = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
		$raw        = explode("# START COLUMNS DO NOT REMOVE THIS LINE",$raw);
		$rraw       = explode("# END COLUMNS DO NOT REMOVE THIS LINE",$raw[1]);

		$file_controller = trim($raw[0])."\n\n";
		$file_controller .= "\t\t\t# START COLUMNS DO NOT REMOVE THIS LINE\n";
		$file_controller .= "\t\t\t".'$this->col = [];'."\n";
		$file_controller .= $scripts."\n";
		$file_controller .= "\t\t\t# END COLUMNS DO NOT REMOVE THIS LINE\n\n";
		$file_controller .= "\t\t\t".trim($rraw[1]);	
		
		file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

		return redirect(Route("ModulsControllerGetStep3",["id"=>$id]));
	}

	public function getStep3($id) {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$row = DB::table('cms_moduls')->where('id',$id)->first();

		$columns = CRUDBooster::getTableColumns($row->table_name);

		if(file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
			$response     = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
			$column_datas = extract_unit($response,"# START FORM DO NOT REMOVE THIS LINE","# END FORM DO NOT REMOVE THIS LINE");			
			$column_datas = str_replace('$this->','$cb_', $column_datas);			
			eval($column_datas);
		}

		$types = [];
		foreach(glob(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components').'/*', GLOB_ONLYDIR) as $dir) {
		    $types[] = basename($dir);
		}

		return view('crudbooster::module_generator.step3',compact('columns','cb_form','types','id'));
	}

	public function getTypeInfo($type='text') {
		header("Content-Type: application/json");
		echo file_get_contents(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/info.json'));
	}

	public function postStep4() {
		$this->cbLoader();	

		$post = Request::all();
		$id = $post['id'];

		$label = $post['label'];
		$name = $post['name'];
		$width = $post['width'];
		$type = $post['type'];
		$option = $post['option'];
		$validation = $post['validation'];

		$row = DB::table('cms_moduls')->where('id',$id)->first();		

		$i = 0;
		$script_form = [];
		foreach($label as $l) {
			
			if($l!='') {				
				
				$form = array();
				$form['label'] = $l;
				$form['name'] = $name[$i];
				$form['type'] = $type[$i];
				$form['validation'] = $validation[$i];
				$form['width'] = $width[$i];
				if($option[$i]) {					
					$form = array_merge($form,$option[$i]);				
				}
				
				foreach($form as $k=>$f) {
					if($f == '') {
						unset($form[$k]);
					}					
				}


				$script_form[$i] = "\t\t\t".'$this->form[] = '.min_var_export($form).";";
			}
			
			$i++;
		}					

		$scripts    = implode("\n",$script_form);
		$raw        = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
		$raw        = explode("# START FORM DO NOT REMOVE THIS LINE",$raw);
		$rraw       = explode("# END FORM DO NOT REMOVE THIS LINE",$raw[1]);

		$top_script = trim($raw[0]);
		$current_scaffolding_form = trim($rraw[0]);	
		$bottom_script = trim($rraw[1]);	

		//IF FOUND OLD, THEN CLEAR IT
		if(strpos($bottom_script, '# OLD START FORM')!==FALSE) {			
			$line_end_count = strlen('# OLD END FORM');
			$line_start_old = strpos($bottom_script, '# OLD START FORM');
			$line_end_old = strpos($bottom_script, '# OLD END FORM') + $line_end_count;
			$get_string = substr($bottom_script, $line_start_old, $line_end_old);
			$bottom_script = str_replace($get_string, '', $bottom_script);
		}

		//ARRANGE THE FULL SCRIPT
		$file_controller = $top_script."\n\n";
		$file_controller .= "\t\t\t# START FORM DO NOT REMOVE THIS LINE\n";
		$file_controller .= "\t\t\t".'$this->form = [];'."\n";
		$file_controller .= $scripts."\n";
		$file_controller .= "\t\t\t# END FORM DO NOT REMOVE THIS LINE\n\n";

		//CREATE A BACKUP SCAFFOLDING TO OLD TAG
		if($current_scaffolding_form) {
			$current_scaffolding_form = preg_split("/\\r\\n|\\r|\\n/", $current_scaffolding_form);
			foreach($current_scaffolding_form as &$c) {
				$c = "\t\t\t//".trim($c);
			}
			$current_scaffolding_form = implode("\n",$current_scaffolding_form);

			$file_controller .= "\t\t\t# OLD START FORM\n";
			$file_controller .= $current_scaffolding_form."\n";
			$file_controller .= "\t\t\t# OLD END FORM\n\n";
		}
		
		$file_controller .= "\t\t\t".trim($bottom_script);
		
		//CREATE FILE CONTROLLER
		file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

		return redirect(Route("ModulsControllerGetStep4",["id"=>$id]));
		
	}

	public function getStep4($id) {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$row = DB::table('cms_moduls')->where('id',$id)->first();

		$data = [];
		$data['id'] = $id;
		if(file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
			$response     = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
			$column_datas = extract_unit($response,"# START CONFIGURATION DO NOT REMOVE THIS LINE","# END CONFIGURATION DO NOT REMOVE THIS LINE");			
			$column_datas = str_replace('$this->','$data[\'cb_', $column_datas);			
			$column_datas = str_replace(' = ','\'] = ', $column_datas);			
			$column_datas = str_replace([' ',"\t"],'', $column_datas);			
			eval($column_datas);
		}

		return view('crudbooster::module_generator.step4',$data);
	}	

	public function postStepFinish() {
		$this->cbLoader();
		$id = Request::input('id');
		$row = DB::table('cms_moduls')->where('id',$id)->first();

		$post = Request::all();

		$post['table'] = $row->table_name;

		$script_config = [];
		$exception = ['_token','id','submit'];
		$i = 0;
		foreach($post as $key=>$val) {
			if(in_array($key, $exception)) continue;		

			if($val != 'true' && $val != 'false') {
				$value = '"'.$val.'"';
			}else{
				$value = $val;
			}

			// if($key == 'orderby') {
			// 	$value = ;
			// }

			$script_config[$i] = "\t\t\t".'$this->'.$key.' = '.$value.';';
			$i++;
		}	

		$scripts    = implode("\n",$script_config);
		$raw        = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
		$raw        = explode("# START CONFIGURATION DO NOT REMOVE THIS LINE",$raw);
		$rraw       = explode("# END CONFIGURATION DO NOT REMOVE THIS LINE",$raw[1]);

		$file_controller = trim($raw[0])."\n\n";
		$file_controller .= "\t\t\t# START CONFIGURATION DO NOT REMOVE THIS LINE\n";		
		$file_controller .= $scripts."\n";
		$file_controller .= "\t\t\t# END CONFIGURATION DO NOT REMOVE THIS LINE\n\n";
		$file_controller .= "\t\t\t".trim($rraw[1]);
		
		file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

		return redirect()->route('ModulsControllerGetIndex')->with(['message'=>trans('crudbooster.alert_update_data_success'),'message_type'=>'success']);
	}
	
	public function postAddSave() {
		$this->cbLoader();

		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation();					
		$this->input_assignment();	

		//Generate Controller 
		$route_basename = basename(Request::get('path'));
		if($this->arr['controller']=='') {
			$this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'),$route_basename);			
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


		$ref_parameter = Request::input('ref_parameter');		
		if(Request::get('return_url')) {			
			CRUDBooster::redirect(Request::get('return_url'),trans("crudbooster.alert_add_data_success"),'success');
		}else{
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
			}
		}
		
	}
	
	
	 
	public function postEditSave($id) {
		$this->cbLoader();

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

		$this->validation();
		$this->input_assignment();

		//Generate Controller 
		$route_basename = basename(Request::get('path'));
		if($this->arr['controller']=='') {
			$this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'),$route_basename);
		}

		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);


		//Refresh Session Roles
		$roles = DB::table('cms_privileges_roles')
		->where('id_cms_privileges',CRUDBooster::myPrivilegeId())
		->join('cms_moduls','cms_moduls.id','=','id_cms_moduls')
		->select('cms_moduls.name','cms_moduls.path','is_visible','is_create','is_read','is_edit','is_delete')
		->get();
		Session::put('admin_privileges_roles',$roles);

		CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans('crudbooster.alert_update_data_success'),'success');
		
	}	
	
}
