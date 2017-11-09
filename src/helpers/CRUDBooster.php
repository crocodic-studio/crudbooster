<?php

namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;
use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Config;
use Validator;

class CRUDBooster
{
    public static function insert($table, $data = [])
    {
        if (! $data['created_at'] && Schema::hasColumn($table, 'created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return DB::table($table)->insertGetId($data);
    }

    public static function get($table, $string_conditions = null, $orderby = null, $limit = null, $skip = null)
    {
        $table = self::parseSqlTable($table);
        $table = $table['table'];
        $query = DB::table($table);
        if ($string_conditions) {
            $query->whereraw($string_conditions);
        }
        if ($orderby) {
            $query->orderbyraw($orderby);
        }
        if ($limit) {
            $query->take($limit);
        }
        if ($skip) {
            $query->skip($skip);
        }

        return $query->get();
    }

    public static function parseSqlTable($table)
    {

        $f = explode('.', $table);

        if (count($f) == 1) {
            return ["table" => $f[0], "database" => cbConfig('MAIN_DB_DATABASE')];
        }
        if (count($f) == 2) {
            return ["database" => $f[0], "table" => $f[1]];
        }

        if (count($f) == 3) {
            return ["table" => $f[0], "schema" => $f[1], "table" => $f[2]];
        }

        return false;
    }

    public static function me()
    {
        return DB::table(cbConfig('USER_TABLE'))->where('id', session('admin_id'))->first();
    }

    public static function myName()
    {
        return session('admin_name');
    }

    public static function myPhoto()
    {
        return session('admin_photo');
    }

    public static function myPrivilege()
    {
        $roles = session('admin_privileges_roles');
        if (! $roles) {
            return;
        }
        foreach ($roles as $role) {
            if ($role->path == CRUDBooster::getModulePath()) {
                return $role;
            }
        }
    }

    private static function getModulePath()
    {
        $adminPathSegments = count(explode('/',config('crudbooster.ADMIN_PATH')));
        return Request::segment(1 + $adminPathSegments);
    }

    public static function isLocked()
    {
        return session('admin_lock');
    }

    public static function isSuperadmin()
    {
        return session('admin_is_superadmin');
    }

    public static function canView()
    {
        return self::canDo('is_visible');
    }

    public static function canUpdate()
    {
        return self::canDo('is_edit');
    }

    public static function canCreate()
    {
        return self::canDo('is_create');
    }

    public static function canRead()
    {
        return self::canDo('is_read');
    }

    public static function canDelete()
    {
        return self::canDo('is_delete');
    }

    public static function canCRUD()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = session('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path !== self::getModulePath()) {
                continue;
            }
            if ($v->is_visible && $v->is_create && $v->is_read && $v->is_edit && $v->is_delete) {
                return true;
            }

            return false;
        }
    }

    public static function getCurrentModule()
    {
        $modulepath = self::getModulePath();
        if (Cache::has('moduls_'.$modulepath)) {
            return Cache::get('moduls_'.$modulepath);
        }

        return DB::table('cms_moduls')->where('path', self::getModulePath())->first();
    }

    public static function getCurrentDashboardId()
    {
        if (request('d') == null) {
            return session('currentDashboardId');
        }
        Session::put('currentDashboardId', request('d'));
        Session::put('currentMenuId', 0);

        return request('d');
    }

    public static function getCurrentMenuId()
    {
        if (request('m') == null) {
            return session('currentMenuId');
        }
        Session::put('currentMenuId', request('m'));
        Session::put('currentDashboardId', 0);

        return request('m');
    }

    public static function sidebarDashboard()
    {

        $menu = DB::table('cms_menus')->where('cms_privileges', self::myPrivilegeId())->where('is_dashboard', 1)->where('is_active', 1)->first() ?: new \stdClass();

        $menu->url = self::menuUrl($menu);

        return $menu;
    }

    public static function myPrivilegeId()
    {
        return session('admin_privileges');
    }

    private static function menuUrl($menu)
    {
        $menu->is_broken = false;
        if ($menu->type == 'Route') {
            return route($menu->path);
        }

        if ($menu->type == 'URL') {
            return $menu->path;
        }

        if ($menu->type == 'Controller & Method') {
            return action($menu->path);
        }

        if ($menu->type == 'Module' || $menu->type == 'Statistic') {
            return self::adminPath($menu->path);
        }

        $menu->is_broken = true;

        return '#';
    }

    public static function adminPath($path = null)
    {
        return url(cbAdminPath().'/'.$path);
    }

    public static function sidebarMenu()
    {
        $menu_active = DB::table('cms_menus')
            ->where('cms_privileges', self::myPrivilegeId())
            ->where('parent_id', 0)->where('is_active', 1)
            ->where('is_dashboard', 0)
            ->orderby('sorting', 'asc')
            ->select('cms_menus.*')
            ->get();

        foreach ($menu_active as &$menu) {

            $url = self::menuUrl($menu);

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");

            $child = DB::table('cms_menus')
                ->where('is_dashboard', 0)
                ->where('is_active', 1)
                ->where('cms_privileges', 'like', '%"'.self::myPrivilegeName().'"%')
                ->where('parent_id', $menu->id)
                ->select('cms_menus.*')
                ->orderby('sorting', 'asc')
                ->get();

            if (count($child)) {
                foreach ($child as &$c) {
                    $url = self::menuUrl($c);
                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }

                $menu->children = $child;
            }
        }

        return $menu_active;
    }

    public static function myPrivilegeName()
    {
        return session('admin_privileges_name');
    }

    public static function deleteConfirm($redirectTo)
    {
        echo 'swal({   
				title: "'.trans('crudbooster.delete_title_confirm').'",   
				text: "'.trans('crudbooster.delete_description_confirm').'",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#ff0000",   
				confirmButtonText: "'.trans('crudbooster.confirmation_yes').'",  
				cancelButtonText: "'.trans('crudbooster.confirmation_no').'",  
				closeOnConfirm: false }, 
				function(){  location.href="'.$redirectTo.'" });';
    }

    public static function getCurrentId()
    {
        $id = session('current_row_id');
        $id = intval($id);
        $id = (! $id) ? Request::segment(4) : $id;
        $id = intval($id);

        return $id;
    }

    public static function getCurrentMethod()
    {
        $action = str_replace(ctrlNamespace(), "", Route::currentRouteAction());
        $atloc = strpos($action, '@') + 1;
        $method = substr($action, $atloc);

        return $method;
    }

    public static function clearCache($name)
    {
        if (Cache::forget($name)) {
            return true;
        }

        return false;
    }

    public static function isColumnNULL($table, $field)
    {
        $cacheKey = 'field_isNull_'.$table.'_'.$field;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            //MySQL & SQL Server
            $isNULL = DB::select(DB::raw("select IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->IS_NULLABLE;
            $isNULL = ($isNULL == 'YES') ? true : false;
        } catch (\Exception $e) {
            $isNULL = false;
        }
        Cache::forever($cacheKey, $isNULL);

        return $isNULL;
    }

    public static function getValueFilter($field)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['value'];
        }
    }

    public static function getSortingFilter($field)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['sorting'];
        }
    }

    public static function getTypeFilter($field)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['type'];
        }
    }

    public static function stringBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    public static function timeAgo($datetime_to, $datetime_from = null, $full = false)
    {
        $datetime_from = ($datetime_from) ?: date('Y-m-d H:i:s');
        $now = new \DateTime;
        if ($datetime_from != '') {
            $now = new \DateTime($datetime_from);
        }
        $ago = new \DateTime($datetime_to);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k.' '.$v.($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (! $full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string).' ' : 'just now';
    }

    public static function sendEmailQueue($queue)
    {
        Config::set('mail.driver', self::getSetting('smtp_driver'));
        Config::set('mail.host', self::getSetting('smtp_host'));
        Config::set('mail.port', self::getSetting('smtp_port'));
        Config::set('mail.username', self::getSetting('smtp_username'));
        Config::set('mail.password', self::getSetting('smtp_password'));

        $html = $queue->email_content;
        $to = $queue->email_recipient;
        $subject = $queue->email_subject;
        $from_email = $queue->email_from_email;
        $from_name = $queue->email_from_name;
        $cc_email = $queue->email_cc_email;
        $attachments = unserialize($queue->email_attachments);

        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use (
            $html,
            $to,
            $subject,
            $from_email,
            $from_name,
            $cc_email,
            $attachments
        ) {
            $message->priority(1);
            $message->to($to);
            $message->from($from_email, $from_name);
            $message->cc($cc_email);

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }

    public static function getSetting($name)
    {
        if (Cache::has('setting_'.$name)) {
            return Cache::get('setting_'.$name);
        }

        $query = DB::table('cms_settings')->where('name', $name)->first();
        Cache::forever('setting_'.$name, $query->content);

        return $query->content;
    }

    public static function sendEmail($config = [])
    {

        Config::set('mail.driver', self::getSetting('smtp_driver'));
        Config::set('mail.host', self::getSetting('smtp_host'));
        Config::set('mail.port', self::getSetting('smtp_port'));
        Config::set('mail.username', self::getSetting('smtp_username'));
        Config::set('mail.password', self::getSetting('smtp_password'));

        $to = $config['to'];
        $data = $config['data'];
        $template = $config['template'];

        $template = CRUDBooster::first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($data as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $attachments = ($config['attachments']) ?: [];

        if ($config['send_at'] != null) {
            $queue = [
                'send_at' => $config['send_at'],
                'email_recipient' => $to,
                'email_from_email' => $template->from_email ?: CRUDBooster::getSetting('email_sender'),
                'email_from_name' => $template->from_name ?: CRUDBooster::getSetting('appname'),
                'email_cc_email' => $template->cc_email,
                'email_subject' => $subject,
                'email_content' => $html,
                'email_attachments' => serialize($attachments),
                'is_sent' => 0,
            ];
            DB::table('cms_email_queues')->insert($queue);

            return true;
        }

        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use ($to, $subject, $template, $attachments) {
            $message->priority(1);
            $message->to($to);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: CRUDBooster::getSetting('appname');
                $message->from($template->from_email, $from_name);
            }

            if ($template->cc_email) {
                $message->cc($template->cc_email);
            }

            if (count($attachments)) {
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            }

            $message->subject($subject);
        });
    }

    public static function first($table, $id)
    {
        $table = self::parseSqlTable($table)['table'];
        if (! is_array($id)) {
            $pk = self::pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }

        $first = DB::table($table);
        foreach ($id as $k => $v) {
            $first->where($k, $v);
        }

        return $first->first();
    }

    public static function pk($table)
    {
        return self::findPrimaryKey($table);
    }

    public static function findPrimaryKey($table)
    {
        if (! $table) {
            return 'id';
        }

        if (self::getCache('table_'.$table, 'primary_key')) {
            return self::getCache('table_'.$table, 'primary_key');
        }
        $table = CRUDBooster::parseSqlTable($table);

        if (! $table['table']) {
            throw new \Exception("parseSqlTable can't determine the table");
        }

        if (env('DB_CONNECTION') == 'sqlsrv') {
            try {
                $query = "
						SELECT Col.Column_Name,Col.Table_Name from 
						    INFORMATION_SCHEMA.TABLE_CONSTRAINTS Tab, 
						    INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE Col 
						WHERE 
						    Col.Constraint_Name = Tab.Constraint_Name
						    AND Col.Table_Name = Tab.Table_Name
						    AND Constraint_Type = 'PRIMARY KEY'
							AND Col.Table_Name = '$table[table]' 
					";
                $keys = DB::select($query);
                $primary_key = $keys[0]->Column_Name;
            } catch (\Exception $e) {
                $primary_key = null;
            }
        } else {
            try {
                $query = "select * from information_schema.COLUMNS where TABLE_SCHEMA = '$table[database]' and TABLE_NAME = '$table[table]' and COLUMN_KEY = 'PRI'";
                $keys = DB::select($query);
                $primary_key = $keys[0]->COLUMN_NAME;
            } catch (\Exception $e) {
                $primary_key = null;
            }
        }

        if (! $primary_key) {
            return 'id';
        }
        self::putCache('table_'.$table, 'primary_key', $primary_key);

        return $primary_key;
    }

    public static function getCache($section, $cache_name)
    {
        if (! Cache::has($section)) {
            return false;
        }
        $cache_open = Cache::get($section);

        return $cache_open[$cache_name];
    }

    public static function putCache($section, $cache_name, $cache_value)
    {
        if (Cache::has($section)) {
            $cache_open = Cache::get($section);
        } else {
            Cache::forever($section, []);
            $cache_open = Cache::get($section);
        }
        $cache_open[$cache_name] = $cache_value;
        Cache::forever($section, $cache_open);

        return true;
    }

    public static function valid($arr = [], $type = 'json')
    {
        $input_arr = Request::all();

        foreach ($arr as $a => $b) {
            if (is_int($a)) {
                $arr[$b] = 'required';
            } else {
                $arr[$a] = $b;
            }
        }

        $validator = Validator::make($input_arr, $arr);

        if (!$validator->fails()) {
            return true;
        }
        $message = $validator->errors()->all();

        if ($type == 'json') {
            $result = [];
            $result['api_status'] = 0;
            $result['api_message'] = implode(', ', $message);
            response()->json($result, 200)->send();
            exit;
        }

        $res = redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput();
        \Session::driver()->save();
        $res->send();
        exit;
    }

    public static function flushCache()
    {
        Cache::flush();
    }

    public static function forgetCache($section, $cache_name)
    {
        if (! Cache::has($section)) {
            return false;
        }
        $open = Cache::get($section);
        unset($open[$cache_name]);
        Cache::forever($section, $open);

        return true;
    }

    public static function newId($table)
    {
        $key = CRUDBooster::findPrimaryKey($table);
        $id = DB::table($table)->max($key) + 1;

        return $id;
    }

    public static function getForeignKey($parent_table, $child_table)
    {
        $parent_table = CRUDBooster::parseSqlTable($parent_table)['table'];
        $child_table = CRUDBooster::parseSqlTable($child_table)['table'];
        if (self::isColumnExists($child_table, 'id_'.$parent_table)) {
            return 'id_'.$parent_table;
        } else {
            return $parent_table.'_id';
        }
    }

    public static function isColumnExists($table, $field)
    {

        if (! $table) {
            throw new Exception("\$table is empty !", 1);
        }
        if (! $field) {
            throw new Exception("\$field is empty !", 1);
        }

        $table = CRUDBooster::parseSqlTable($table);

        if (self::getCache('table_'.$table, 'column_'.$field)) {
            return self::getCache('table_'.$table, 'column_'.$field);
        }

        $result = DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table AND COLUMN_NAME = :field', [
                'database' => $table['database'],
                'table' => $table['table'],
                'field' => $field,
            ]);

        if (count($result) > 0) {
            self::putCache('table_'.$table, 'column_'.$field, 1);

            return true;
        }

        self::putCache('table_'.$table, 'column_'.$field, 0);

        return false;
    }

    public static function getTableForeignKey($fieldName)
    {
        $table = null;
        if (substr($fieldName, 0, 3) == 'id_' || substr($fieldName, -3) == '_id') {
            $table = str_replace(['_id', 'id_'], '', $fieldName);
        }

        return $table;
    }

    public static function isForeignKey($fieldName)
    {
        $table = self::getTableForeignKey($fieldName);
        $cacheKey = 'isForeignKey_'.$fieldName;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        if (!$table) {
            return false;
        }

        if (Schema::hasTable($table)) {
            Cache::forever($cacheKey, true);
            return true;
        }

        Cache::forever($cacheKey, false);
        return false;
    }

    public static function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        $params = Request::all();
        $mainpath = trim(self::mainpath(), '/');

        if ($params['filter_column'] && $singleSorting) {
            foreach ($params['filter_column'] as $k => $filter) {
                foreach ($filter as $t => $val) {
                    if ($t == 'sorting') {
                        unset($params['filter_column'][$k]['sorting']);
                    }
                }
            }
        }

        $params['filter_column'][$key][$type] = $value;

        if (isset($params)) {
            return $mainpath.'?'.http_build_query($params);
        } else {
            return $mainpath.'?filter_column['.$key.']['.$type.']='.$value;
        }
    }

    public static function mainpath($path = null)
    {
        $controllerName = strtok(Route::currentRouteAction(), '@');
        $controllerName = array_pop(explode('\\', $controllerName));
        $route_url = route($controllerName.'GetIndex');

        if (! $path) {
            return trim($route_url, '/');
        }

        if (substr($path, 0, 1) == '?') {
            return trim($route_url, '/').$path;
        }

        return $route_url.'/'.$path;
    }

    public static function insertLog($description)
    {
        $log = [
            'created_at' => date('Y-m-d H:i:s'),
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'url' => Request::url(),
            'description' => $description,
            'id_cms_users' => self::myId(),
        ];
        DB::table('cms_logs')->insert($log);
    }

    public static function myId()
    {
        return session('admin_id');
    }

    public static function referer()
    {
        return Request::server('HTTP_REFERER');
    }

    public static function listTables()
    {
        $multiple_db = cbConfig('MULTIPLE_DATABASE_MODULE');
        $multiple_db = ($multiple_db) ? $multiple_db : [];
        $db_database = cbConfig('MAIN_DB_DATABASE');

        if ($multiple_db) {
            try {
                $multiple_db[] = cbConfig('MAIN_DB_DATABASE');
                $query_table_schema = implode("','", $multiple_db);
                $tables = DB::select("SELECT CONCAT(TABLE_SCHEMA,'.',TABLE_NAME) FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA != 'mysql' AND TABLE_SCHEMA != 'performance_schema' AND TABLE_SCHEMA != 'information_schema' AND TABLE_SCHEMA != 'phpmyadmin' AND TABLE_SCHEMA IN ('$query_table_schema')");
            } catch (\Exception $e) {
                $tables = [];
            }
            return $tables;
        }

        try {
            $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '".$db_database."'");
        } catch (\Exception $e) {
            $tables = [];
        }


        return $tables;
    }

    public static function getUrlParameters($exception = null)
    {
        @$get = $_GET;
        $inputhtml = '';

        if (! $get) {
            return $inputhtml;
        }
        if (is_array($exception)) {
            foreach ($exception as $e) {
                unset($get[$e]);
            }
        }

        $string_parameters = http_build_query($get);
        $string_parameters_array = explode('&', $string_parameters);
        foreach ($string_parameters_array as $s) {
            $part = explode('=', $s);
            $name = urldecode($part[0]);
            $value = urldecode($part[1]);
            $inputhtml .= "<input type='hidden' name='$name' value='$value'/>";
        }

        return $inputhtml;
    }

    public static function authAPI()
    {
        if (self::getSetting('api_debug_mode') !== 'false') {
            return ;
        }

        $result = [];
        $validator = Validator::make([

            'X-Authorization-Token' => Request::header('X-Authorization-Token'),
            'X-Authorization-Time' => Request::header('X-Authorization-Time'),
            'useragent' => Request::header('User-Agent'),
        ], [
                'X-Authorization-Token' => 'required',
                'X-Authorization-Time' => 'required',
                'useragent' => 'required',
            ]);

        if ($validator->fails()) {
            $message = $validator->errors()->all();
            $result['api_status'] = 0;
            $result['api_message'] = implode(', ', $message);
           response()->json($result, 200)->send();
            exit;
        }

        $user_agent = Request::header('User-Agent');
        $time = Request::header('X-Authorization-Time');

        $keys = DB::table('cms_apikey')->where('status', 'active')->pluck('screetkey');
        $server_token = [];
        $server_token_screet = [];
        foreach ($keys as $key) {
            $server_token[] = md5($key.$time.$user_agent);
            $server_token_screet[] = $key;
        }

        $sender_token = Request::header('X-Authorization-Token');

        if (! Cache::has($sender_token) && ! in_array($sender_token, $server_token)) {
            $result['api_status'] = false;
            $result['api_message'] = "THE TOKEN IS NOT MATCH WITH SERVER TOKEN";
            $result['sender_token'] = $sender_token;
            $result['server_token'] = $server_token;
            response()->json($result, 200)->send();
            exit;
        }

        if (Cache::has($sender_token) && Cache::get($sender_token) != $user_agent) {
            $result['api_status'] = false;
            $result['api_message'] = "THE TOKEN IS ALREADY BUT NOT MATCH WITH YOUR DEVICE";
            $result['sender_token'] = $sender_token;
            $result['server_token'] = $server_token;
            response()->json($result, 200)->send();
            exit;
        }

        $id = array_search($sender_token, $server_token);
        $server_screet = $server_token_screet[$id];
        DB::table('cms_apikey')->where('screetkey', $server_screet)->increment('hit');

        $expired_token = date('Y-m-d H:i:s', strtotime('+5 seconds'));
        Cache::put($sender_token, $user_agent, $expired_token);
    }

    public static function sendNotification($config = [])
    {
        $content = $config['content'];
        $to = $config['to'];
        $id_cms_users = $config['id_cms_users'];
        $id_cms_users = ($id_cms_users) ?: [CRUDBooster::myId()];
        foreach ($id_cms_users as $id) {
            $notif = [
                'created_at' => date('Y-m-d H:i:s'),
                'id_cms_users' => $id,
                'content' => $content,
                'is_read' => 0,
                'url' => $to,
            ];
            DB::table('cms_notifications')->insert($notif);
        }

        return true;
    }

    public static function sendFCM($regID = [], $data)
    {
        if (! $data['title'] || ! $data['content']) {
            return 'title , content null !';
        }

        $apikey = CRUDBooster::getSetting('google_fcm_key');
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'registration_ids' => $regID,
            'data' => $data,
            'content_available' => true,
            'notification' => [
                'sound' => 'default',
                'badge' => 0,
                'title' => trim(strip_tags($data['title'])),
                'body' => trim(strip_tags($data['content'])),
            ],
            'priority' => 'high',
        ];
        $headers = [
            'Authorization:key='.$apikey,
            'Content-Type:application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $chresult = curl_exec($ch);
        curl_close($ch);

        return $chresult;
    }

    public static function isExistsController($table)
    {
        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        $path = base_path(controllers_dir());
        $path2 = base_path(controllers_dir()."ControllerMaster/");
        if (file_exists($path.'Admin'.$controllername.'.php') || file_exists($path2.'Admin'.$controllername.'.php') || file_exists($path2.$controllername.'.php')) {
            return true;
        }

        return false;
    }

    public static function generateAPI($controller_name, $table_name, $permalink, $method_type = 'post')
    {
        $php = '<?php '.view('CbApiGen::api_stub', compact('controller_name', 'table_name', 'permalink', 'method_type'))->render();
        $path = base_path(controllers_dir());
        file_put_contents($path.'Api'.$controller_name.'Controller.php', $php);
    }

    public static function generateController($table, $name = null)
    {
        return ControllerGenerator::generateController($table, $name);
    }

    public static function getTableColumns($table)
    {
        //$cols = DB::getSchemaBuilder()->getColumnListing($table);
        $table = CRUDBooster::parseSqlTable($table);
        $cols = collect(DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table', [
                'database' => $table['database'],
                'table' => $table['table'],
            ]))->map(function ($x) {
            return (array) $x;
        })->toArray();

        $result = $cols;

        $new_result = [];
        foreach ($result as $ro) {
            $new_result[] = $ro['COLUMN_NAME'];
        }

        return $new_result;
    }

    public static function getNameTable($columns)
    {
        $name_col_candidate = cbConfig('NAME_FIELDS_CANDIDATE');
        $name_col_candidate = explode(',', $name_col_candidate);
        $name_col = '';
        foreach ($columns as $c) {
            foreach ($name_col_candidate as $cc) {
                if (strpos($c, $cc) !== false) {
                    $name_col = $c;
                    break;
                }
            }
            if ($name_col) {
                break;
            }
        }
        if ($name_col == '') {
            $name_col = 'id';
        }

        return $name_col;
    }

    public static function getFieldType($table, $field)
    {
        $field = 'field_type_'.$table.'_'.$field;

        return Cache::rememberForever($field, function () use ($table, $field) {
            try {
                //MySQL & SQL Server
                $typedata = DB::select(DB::raw("select DATA_TYPE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->DATA_TYPE;
            } catch (\Exception $e) {

            }

            if (! $typedata) {
                $typedata = 'varchar';
            }

            return $typedata;
        });
    }

    public static function backWithMsg($msg, $type = 'success')
    {
        return redirect()->back()->with(['message_type' => $type, 'message' => $msg]);
    }

    public static function routeController($prefix, $controller, $namespace = null)
    {

        $prefix = trim($prefix, '/').'/';

        $namespace = ($namespace) ?: ctrlNamespace();

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);

            $controller_class = new \ReflectionClass($namespace.'\\'.$controller);
            $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class == 'Illuminate\Routing\Controller' || $method->name == 'getIndex') {
                    continue;
                }
                if (substr($method->name, 0, 3) == 'get') {
                    $method_name = substr($method->name, 3);
                    $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                    $slug = strtolower(implode('-', $slug));
                    $slug = ($slug == 'index') ? '' : $slug;
                    Route::get($prefix.$slug.$wildcards, ['uses' => $controller.'@'.$method->name, 'as' => $controller.'Get'.$method_name]);
                } elseif (substr($method->name, 0, 4) == 'post') {
                    $method_name = substr($method->name, 4);
                    $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                    Route::post($prefix.strtolower(implode('-', $slug)).$wildcards, [
                            'uses' => $controller.'@'.$method->name,
                            'as' => $controller.'Post'.$method_name,
                        ]);
                }
            }
        } catch (\Exception $e) {

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

    public static function denyAccess()
    {
        static::redirect(static::adminPath(), trans('crudbooster.denied_access'));
    }

    public static function redirect($to, $message, $type = 'warning')
    {
        if (Request::ajax()) {
            response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $to])->send();
            exit;
        }
        redirect($to)->with(['message' => $message, 'message_type' => $type])->prepare(request())->send();
        Session::driver()->save();
        exit;
    }

    public static function icon($icon)
    {
        return '<i class=\'fa fa-'.$icon.'\'></i>';
    }

    private static function canDo($verb)
    {
        if (self::isSuperadmin()) {
            return true;
        }

        foreach (session('admin_privileges_roles') as $role) {
            if ($role->path == self::getModulePath()) {
                return (bool) $role->{$verb};
            }
        }
    }

    public static function componentsTypePath()
    {
        return base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/');
    }
}
