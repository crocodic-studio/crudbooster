<?php 
namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Validation\ValidationException;
use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Validator;

class CB  {

    use RouteTrait, File;

    /**
     * @return Auth
     */
    public function auth() {
        return (new Auth());
    }

    public function dummyPhoto() {
        return asset('vendor/crudbooster/avatar.jpg');
    }
    /**
     *	Comma-delimited data output from the child table
     */
    public function echoSelect2Mult($values, $table, $id, $name) {
        $values = explode(",", $values);
        return implode(", ", DB::table($table)->whereIn($id, $values)->pluck($name)->toArray());
    }

    public function getSetting($name)
    {
        if (Cache::has('setting_'.$name)) {
            return Cache::get('setting_'.$name);
        }

        $query = DB::table('cms_settings')->where('name', $name)->first();
        Cache::forever('setting_'.$name, $query->content);

        return $query->content;
    }

    public function insert($table, $data = [])
    {
        $data['id'] = DB::table($table)->max('id') + 1;
        if (! $data['created_at']) {
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
        }

        if (DB::table($table)->insert($data)) {
            return $data['id'];
        } else {
            return false;
        }
    }

    public function first($table, $id)
    {
        if (is_array($id)) {
            $first = DB::table($table);
            foreach ($id as $k => $v) {
                $first->where($k, $v);
            }

            return $first->first();
        } else {
            $pk = self::pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }
    }

    public function get($table, $string_conditions = null, $orderby = null, $limit = null, $skip = null)
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

    public function me()
    {
        return DB::table(config('crudbooster.USER_TABLE'))->where('id', Session::get('admin_id'))->first();
    }

    public function myId()
    {
        return Session::get('admin_id');
    }

    public function isSuperadmin()
    {
        return Session::get('admin_is_superadmin');
    }

    public function myName()
    {
        return Session::get('admin_name');
    }

    public function myPhoto()
    {
        return Session::get('admin_photo');
    }

    public function myPrivilege()
    {
        $roles = Session::get('admin_privileges_roles');
        if ($roles) {
            foreach ($roles as $role) {
                if ($role->path == cb()->getModulePath()) {
                    return $role;
                }
            }
        }
    }

    public function myPrivilegeId()
    {
        return Session::get('admin_privileges');
    }

    public function myPrivilegeName()
    {
        return Session::get('admin_privileges_name');
    }

    public function isLocked()
    {
        return Session::get('admin_lock');
    }

    public function redirectBack($message, $type = 'warning')
    {

        if (Request::ajax()) {
            $resp = response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $_SERVER['HTTP_REFERER']])->send();
            exit;
        } else {
            $resp = redirect()->back()->with(['message' => $message, 'message_type' => $type]);
            Session::driver()->save();
            $resp->send();
            exit;
        }
    }

    public function redirect($to, $message, $type = 'warning')
    {

        if (Request::ajax()) {
            $resp = response()->json(['message' => $message, 'type' => $type, 'redirect_url' => $to])->send();
            exit;
        } else {
            $resp = redirect($to)->with(['message' => $message, 'type' => $type]);
            Session::driver()->save();
            $resp->send();
            exit;
        }
    }

    public function isView()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_visible;
            }
        }
    }

    public function isUpdate()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_edit;
            }
        }
    }

    public function isCreate()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_create;
            }
        }
    }

    public function isRead()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_read;
            }
        }
    }

    public function isDelete()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                return (bool) $v->is_delete;
            }
        }
    }

    public function isCRUD()
    {
        if (self::isSuperadmin()) {
            return true;
        }

        $session = Session::get('admin_privileges_roles');
        foreach ($session as $v) {
            if ($v->path == self::getModulePath()) {
                if ($v->is_visible && $v->is_create && $v->is_read && $v->is_edit && $v->is_delete) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function getCurrentModule()
    {
        $modulepath = self::getModulePath();

        if (Cache::has('moduls_'.$modulepath)) {
            return Cache::get('moduls_'.$modulepath);
        } else {

            $module = DB::table('cms_moduls')->where('path', self::getModulePath())->first();

            return ($module)?:$modulepath;
        }
    }

    public function getCurrentDashboardId()
    {
        if (Request::get('d') != null) {
            Session::put('currentDashboardId', Request::get('d'));
            Session::put('currentMenuId', 0);

            return Request::get('d');
        } else {
            return Session::get('currentDashboardId');
        }
    }

    public function getCurrentMenuId()
    {
        if (Request::get('m') != null) {
            Session::put('currentMenuId', Request::get('m'));
            Session::put('currentDashboardId', 0);

            return Request::get('m');
        } else {
            return Session::get('currentMenuId');
        }
    }

    public function sidebarDashboard()
    {

        $menu = DB::table('cms_menus')
            ->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")
            ->where('is_dashboard', 1)
            ->where('is_active', 1)
            ->first();

        if($menu) {
            switch ($menu->type) {
                case 'Route':
                    $url = route($menu->path);
                    break;
                default:
                case 'URL':
                    $url = $menu->path;
                    break;
                case 'Controller & Method':
                    $url = action($menu->path);
                    break;
                case 'Module':
                case 'Statistic':
                    $url = self::adminPath($menu->path);
                    break;
            }

            @$menu->url = $url;
        }


        return $menu;
    }

    public function sidebarMenu()
    {
        $menu_active = DB::table('cms_menus')
            ->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")
            ->where('parent_id', 0)
            ->where('is_active', 1)
            ->where('is_dashboard', 0)
            ->orderby('sorting', 'asc')
            ->select('cms_menus.*')->get();

        foreach ($menu_active as &$menu) {

            try {
                switch ($menu->type) {
                    case 'Route':
                        $url = route($menu->path);
                        break;
                    default:
                    case 'URL':
                        $url = $menu->path;
                        break;
                    case 'Controller & Method':
                        $url = action($menu->path);
                        break;
                    case 'Module':
                    case 'Statistic':
                        $url = self::adminPath($menu->path);
                        break;
                }

                $menu->is_broken = false;
            } catch (\Exception $e) {
                $url = "#";
                $menu->is_broken = true;
            }

            $menu->url = $url;
            $menu->url_path = trim(str_replace(url('/'), '', $url), "/");

            $child = DB::table('cms_menus')
                ->whereRaw("cms_menus.id IN (select id_cms_menus from cms_menus_privileges where id_cms_privileges = '".self::myPrivilegeId()."')")
                ->where('is_dashboard', 0)
                ->where('is_active', 1)
                ->where('parent_id', $menu->id)
                ->select('cms_menus.*')
                ->orderby('sorting', 'asc')
                ->get();
            if (count($child)) {

                foreach ($child as &$c) {

                    try {
                        switch ($c->type) {
                            case 'Route':
                                $url = route($c->path);
                                break;
                            default:
                            case 'URL':
                                $url = $c->path;
                                break;
                            case 'Controller & Method':
                                $url = action($c->path);
                                break;
                            case 'Module':
                            case 'Statistic':
                                $url = self::adminPath($c->path);
                                break;
                        }
                        $c->is_broken = false;
                    } catch (\Exception $e) {
                        $url = "#";
                        $c->is_broken = true;
                    }

                    $c->url = $url;
                    $c->url_path = trim(str_replace(url('/'), '', $url), "/");
                }

                $menu->children = $child;
            }
        }

        return $menu_active;
    }

    public function deleteConfirm($redirectTo)
    {
        echo "swal({   
				title: \"".trans('crudbooster.delete_title_confirm')."\",   
				text: \"".trans('crudbooster.delete_description_confirm')."\",   
				type: \"warning\",   
				showCancelButton: true,   
				confirmButtonColor: \"#ff0000\",   
				confirmButtonText: \"".trans('crudbooster.confirmation_yes')."\",  
				cancelButtonText: \"".trans('crudbooster.confirmation_no')."\",  
				closeOnConfirm: false }, 
				function(){  location.href=\"$redirectTo\" });";
    }

    public function getModulePath()
    {
        // Check to position of admin_path
        if(config("crudbooster.ADMIN_PATH")) {
            $adminPathSegments = explode('/', Request::path());
            $no = 1;
            foreach($adminPathSegments as $path) {
                if($path == config("crudbooster.ADMIN_PATH")) {
                    $segment = $no+1;
                    break;
                }
                $no++;
            }
        } else {
            $segment = 1;
        }

        return Request::segment($segment);
    }

    public function mainpath($path = null)
    {

        $controllername = str_replace(["\crocodicstudio\crudbooster\controllers\\", "App\Http\Controllers\\"], "", strtok(Route::currentRouteAction(), '@'));
        $route_url = route($controllername.'GetIndex');

        if ($path) {
            if (substr($path, 0, 1) == '?') {
                return trim($route_url, '/').$path;
            } else {
                return $route_url.'/'.$path;
            }
        } else {
            return trim($route_url, '/');
        }
    }

    public function adminPath($path = null)
    {
        return url(config('crudbooster.ADMIN_PATH').'/'.$path);
    }

    public function getCurrentId()
    {
        $id = Session::get('current_row_id');
        $id = intval($id);
        $id = (! $id) ? Request::segment(4) : $id;
        $id = intval($id);

        return $id;
    }

    public function getCurrentMethod()
    {
        $action = str_replace("App\Http\Controllers", "", Route::currentRouteAction());
        $atloc = strpos($action, '@') + 1;
        $method = substr($action, $atloc);

        return $method;
    }

    public function clearCache($name)
    {
        if (Cache::forget($name)) {
            return true;
        } else {
            return false;
        }
    }

    public function isColumnNULL($table, $field)
    {
        if (Cache::has('field_isNull_'.$table.'_'.$field)) {
            return Cache::get('field_isNull_'.$table.'_'.$field);
        }

        try {
            //MySQL & SQL Server
            $isNULL = DB::select(DB::raw("select IS_NULLABLE from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table' and COLUMN_NAME = '$field'"))[0]->IS_NULLABLE;
            $isNULL = ($isNULL == 'YES') ? true : false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        } catch (\Exception $e) {
            $isNULL = false;
            Cache::forever('field_isNull_'.$table.'_'.$field, $isNULL);
        }

        return $isNULL;
    }

    public function getFieldType($table, $field)
    {
        if (Cache::has('field_type_'.$table.'_'.$field)) {
            return Cache::get('field_type_'.$table.'_'.$field);
        }

        $typedata = Cache::rememberForever('field_type_'.$table.'_'.$field, function () use ($table, $field) {

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

        return $typedata;
    }

    public function getValueFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['value'];
        }
    }

    public function getSortingFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['sorting'];
        }
    }

    public function getTypeFilter($field)
    {
        $filter = Request::get('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['type'];
        }
    }

    public function stringBetween($string, $start, $end)
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

    public function timeAgo($datetime_to, $datetime_from = null, $full = false)
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

    public function sendEmailQueue($queue)
    {
        \Config::set('mail.driver', self::getSetting('smtp_driver'));
        \Config::set('mail.host', self::getSetting('smtp_host'));
        \Config::set('mail.port', self::getSetting('smtp_port'));
        \Config::set('mail.username', self::getSetting('smtp_username'));
        \Config::set('mail.password', self::getSetting('smtp_password'));

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

    public function sendEmail($config = [])
    {

        \Config::set('mail.driver', self::getSetting('smtp_driver'));
        \Config::set('mail.host', self::getSetting('smtp_host'));
        \Config::set('mail.port', self::getSetting('smtp_port'));
        \Config::set('mail.username', self::getSetting('smtp_username'));
        \Config::set('mail.password', self::getSetting('smtp_password'));

        $to = $config['to'];
        $data = $config['data'];
        $template = $config['template'];

        $template = cb()->first('cms_email_templates', ['slug' => $template]);
        $html = $template->content;
        foreach ($data as $key => $val) {
            $html = str_replace('['.$key.']', $val, $html);
            $template->subject = str_replace('['.$key.']', $val, $template->subject);
        }
        $subject = $template->subject;
        $attachments = ($config['attachments']) ?: [];

        if ($config['send_at'] != null) {
            $a = [];
            $a['send_at'] = $config['send_at'];
            $a['email_recipient'] = $to;
            $a['email_from_email'] = $template->from_email ?: cb()->getSetting('email_sender');
            $a['email_from_name'] = $template->from_name ?: cb()->getSetting('appname');
            $a['email_cc_email'] = $template->cc_email;
            $a['email_subject'] = $subject;
            $a['email_content'] = $html;
            $a['email_attachments'] = serialize($attachments);
            $a['is_sent'] = 0;
            DB::table('cms_email_queues')->insert($a);

            return true;
        }

        \Mail::send("crudbooster::emails.blank", ['content' => $html], function ($message) use ($to, $subject, $template, $attachments) {
            $message->priority(1);
            $message->to($to);

            if ($template->from_email) {
                $from_name = ($template->from_name) ?: cb()->getSetting('appname');
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

    /**
     * @param array $rules
     * @param array $input
     * @throws CBValidationException
     */
    public function validation($rules = [], $input = [])
    {
        $inputs = $input?:request()->all();

        foreach ($rules as $a => $b) {
            if (is_int($a)) {
                $rules[$b] = 'required';
            } else {
                $rules[$a] = $b;
            }
        }

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $message = $validator->errors()->all();
            throw new CBValidationException(implode(', ', $message));
        }
    }

    public function parseSqlTable($table)
    {

        $f = explode('.', $table);

        if (count($f) == 1) {
            return ["table" => $f[0], "database" => config('crudbooster.MAIN_DB_DATABASE')];
        } elseif (count($f) == 2) {
            return ["database" => $f[0], "table" => $f[1]];
        } elseif (count($f) == 3) {
            return ["table" => $f[0], "schema" => $f[1], "table" => $f[2]];
        }

        return false;
    }

    public function putCache($section, $cache_name, $cache_value)
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

    public function getCache($section, $cache_name)
    {

        if (Cache::has($section)) {
            $cache_open = Cache::get($section);

            return $cache_open[$cache_name];
        } else {
            return false;
        }
    }

    public function flushCache()
    {
        Cache::flush();
    }

    public function forgetCache($section, $cache_name)
    {
        if (Cache::has($section)) {
            $open = Cache::get($section);
            unset($open[$cache_name]);
            Cache::forever($section, $open);

            return true;
        } else {
            return false;
        }
    }

    public function pk($table)
    {
        return self::findPrimaryKey($table);
    }

//     public function findPrimaryKey($table)
//     {
//         if (! $table) {
//             return 'id';
//         }

//         if (self::getCache('table_'.$table, 'primary_key')) {
//             return self::getCache('table_'.$table, 'primary_key');
//         }
//         $table = cb()->parseSqlTable($table);

//         if (! $table['table']) {
//             throw new \Exception("parseSqlTable can't determine the table");
//         }
//         $query = config('database.connections.'.config('database.default').'.driver') == 'pgsql' ? "select * from information_schema.key_column_usage WHERE TABLE_NAME = '$table[table]'" : "select * from information_schema.COLUMNS where TABLE_SCHEMA = '$table[database]' and TABLE_NAME = '$table[table]' and COLUMN_KEY = 'PRI'";
//         $keys = DB::select($query);
//         $primary_key = $keys[0]->COLUMN_NAME;
//         if ($primary_key) {
//             self::putCache('table_'.$table, 'primary_key', $primary_key);

//             return $primary_key;
//         } else {
//             return 'id';
//         }
//     }

    public function findPrimaryKey($table)
    {
        if(!$table)
        {
            return 'id';
        }

        $pk = DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey();
        if(!$pk) {
            return null;
        }
        return $pk->getColumns()[0];
    }

    public function newId($table)
    {
        $key = cb()->findPrimaryKey($table);
        $id = DB::table($table)->max($key) + 1;

        return $id;
    }

    public function isColumnExists($table, $field)
    {

        if (! $table) {
            throw new Exception("\$table is empty !", 1);
        }
        if (! $field) {
            throw new Exception("\$field is empty !", 1);
        }

        $table = cb()->parseSqlTable($table);

        // if(self::getCache('table_'.$table,'column_'.$field)) {
        // 	return self::getCache('table_'.$table,'column_'.$field);
        // }

        if (Schema::hasColumn($table['table'], $field)) {
            // self::putCache('table_'.$table,'column_'.$field,1);
            return true;
        } else {
            // self::putCache('table_'.$table,'column_'.$field,0);
            return false;
        }
    }

    public function getForeignKey($parent_table, $child_table)
    {
        $parent_table = cb()->parseSqlTable($parent_table)['table'];
        $child_table = cb()->parseSqlTable($child_table)['table'];
        if (Schema::hasColumn($child_table, 'id_'.$parent_table)) {
            return 'id_'.$parent_table;
        } else {
            return $parent_table.'_id';
        }
    }

    public function getTableForeignKey($fieldName)
    {
        $table = null;
        if (substr($fieldName, 0, 3) == 'id_') {
            $table = substr($fieldName, 3);
        } elseif (substr($fieldName, -3) == '_id') {
            $table = substr($fieldName, 0, (strlen($fieldName) - 3));
        }

        return $table;
    }

    public function isForeignKey($fieldName)
    {
        if (substr($fieldName, 0, 3) == 'id_') {
            $table = substr($fieldName, 3);
        } elseif (substr($fieldName, -3) == '_id') {
            $table = substr($fieldName, 0, (strlen($fieldName) - 3));
        }

        if (Cache::has('isForeignKey_'.$fieldName)) {
            return Cache::get('isForeignKey_'.$fieldName);
        } else {
            if ($table) {
                $hasTable = Schema::hasTable($table);
                if ($hasTable) {
                    Cache::forever('isForeignKey_'.$fieldName, true);

                    return true;
                } else {
                    Cache::forever('isForeignKey_'.$fieldName, false);

                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
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

    public function insertLog($description, $details = '')
    {
        if (cb()->getSetting('api_debug_mode')) {
            $a = [];
            $a['created_at'] = date('Y-m-d H:i:s');
            $a['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $a['useragent'] = $_SERVER['HTTP_USER_AGENT'];
            $a['url'] = Request::url();
            $a['description'] = $description;
            $a['details'] = $details;
            $a['id_cms_users'] = self::myId();
            DB::table('cms_logs')->insert($a);
        }
    }

    public function referer()
    {
        return Request::server('HTTP_REFERER');
    }

    public function listTables()
    {
        $tables = [];
        $multiple_db = config('crudbooster.MULTIPLE_DATABASE_MODULE');
        $multiple_db = ($multiple_db) ? $multiple_db : [];
        $db_database = config('crudbooster.MAIN_DB_DATABASE');

        if ($multiple_db) {
            try {
                $multiple_db[] = config('crudbooster.MAIN_DB_DATABASE');
                $query_table_schema = implode("','", $multiple_db);
                $tables = DB::select("SELECT CONCAT(TABLE_SCHEMA,'.',TABLE_NAME) FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA != 'mysql' AND TABLE_SCHEMA != 'performance_schema' AND TABLE_SCHEMA != 'information_schema' AND TABLE_SCHEMA != 'phpmyadmin' AND TABLE_SCHEMA IN ('$query_table_schema')");
            } catch (\Exception $e) {
                $tables = [];
            }
        } else {
            try {
                $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.Tables WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = '".$db_database."'");
            } catch (\Exception $e) {
                $tables = [];
            }
        }

        return $tables;
    }

    public function getUrlParameters($exception = null)
    {
        @$get = $_GET;
        $inputhtml = '';

        if ($get) {

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
                if ($name) {
                    $inputhtml .= "<input type='hidden' name='$name' value='$value'/>\n";
                }
            }
        }

        return $inputhtml;
    }

    public function authAPI()
    {

        $allowedUserAgent = config('crudbooster.API_USER_AGENT_ALLOWED');
        $user_agent = Request::header('User-Agent');
        $time = Request::header('X-Authorization-Time');

        if ($allowedUserAgent && count($allowedUserAgent)) {
            $userAgentValid = false;
            foreach ($allowedUserAgent as $a) {
                if (stripos($user_agent, $a) !== false) {
                    $userAgentValid = true;
                    break;
                }
            }
            if ($userAgentValid == false) {
                $result['api_status'] = false;
                $result['api_message'] = "THE DEVICE AGENT IS INVALID";
                $res = response()->json($result, 200);
                $res->send();
                exit;
            }
        }

        if (self::getSetting('api_debug_mode') == 'false') {

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
                $res = response()->json($result, 200);
                $res->send();
                exit;
            }

            $keys = DB::table('cms_apikey')->where('status', 'active')->pluck('screetkey');
            $server_token = [];
            $server_token_screet = [];
            foreach ($keys as $key) {
                $server_token[] = md5($key.$time.$user_agent);
                $server_token_screet[] = $key;
            }

            $sender_token = Request::header('X-Authorization-Token');

            if (! Cache::has($sender_token)) {
                if (! in_array($sender_token, $server_token)) {
                    $result['api_status'] = false;
                    $result['api_message'] = "THE TOKEN IS NOT MATCH WITH SERVER TOKEN";
                    $res = response()->json($result, 200);
                    $res->send();
                    exit;
                }
            } else {
                if (Cache::get($sender_token) != $user_agent) {
                    $result['api_status'] = false;
                    $result['api_message'] = "THE TOKEN IS ALREADY BUT NOT MATCH WITH YOUR DEVICE";
                    $res = response()->json($result, 200);
                    $res->send();
                    exit;
                }
            }

            $id = array_search($sender_token, $server_token);
            $server_screet = $server_token_screet[$id];
            DB::table('cms_apikey')->where('screetkey', $server_screet)->increment('hit');

            $expired_token = date('Y-m-d H:i:s', strtotime('+5 seconds'));
            Cache::put($sender_token, $user_agent, $expired_token);
        }
    }

    public function sendNotification($config = [])
    {
        $content = $config['content'];
        $to = $config['to'];
        $id_cms_users = $config['id_cms_users'];
        $id_cms_users = ($id_cms_users) ?: [cb()->myId()];
        foreach ($id_cms_users as $id) {
            $a = [];
            $a['created_at'] = date('Y-m-d H:i:s');
            $a['id_cms_users'] = $id;
            $a['content'] = $content;
            $a['is_read'] = 0;
            $a['url'] = $to;
            DB::table('cms_notifications')->insert($a);
        }

        return true;
    }

    public function sendFCM($regID = [], $data)
    {
        if (! $data['title'] || ! $data['content']) {
            return 'title , content null !';
        }

        $apikey = cb()->getSetting('google_fcm_key');
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

    public function getTableColumns($table)
    {
        //$cols = DB::getSchemaBuilder()->getColumnListing($table);
        $table = cb()->parseSqlTable($table);
        $cols = collect(DB::select('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table', [
            'database' => $table['database'],
            'table' => $table['table'],
        ]))->map(function ($x) {
            return (array) $x;
        })->toArray();

        $result = [];
        $result = $cols;

        $new_result = [];
        foreach ($result as $ro) {
            $new_result[] = $ro['COLUMN_NAME'];
        }

        return $new_result;
    }

    public function getNameTable($columns)
    {
        $name_col_candidate = config('crudbooster.NAME_FIELDS_CANDIDATE');
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

    public function isExistsController($table)
    {
        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        $path = base_path("app/Http/Controllers/");
        $path2 = base_path("app/Http/Controllers/ControllerMaster/");
        if (file_exists($path.'Admin'.$controllername.'.php') || file_exists($path2.'Admin'.$controllername.'.php') || file_exists($path2.$controllername.'.php')) {
            return true;
        } else {
            return false;
        }
    }

    public function generateAPI($controller_name, $table_name, $permalink, $method_type = 'post')
    {
        $php = '
		<?php namespace App\Http\Controllers;

		use Session;
		use Request;
		use DB;
		use CRUDBooster;

		class Api'.$controller_name.'Controller extends \crocodicstudio\crudbooster\controllers\ApiController {

		    function __construct() {    
				$this->table       = "'.$table_name.'";        
				$this->permalink   = "'.$permalink.'";    
				$this->method_type = "'.$method_type.'";    
		    }
		';

        $php .= "\n".'
		    public function hook_before(&$postdata) {
		        //This method will be execute before run the main process

		    }';

        $php .= "\n".'
		    public function hook_query(&$query) {
		        //This method is to customize the sql query

		    }';

        $php .= "\n".'
		    public function hook_after($postdata,&$result) {
		        //This method will be execute after run the main process

		    }';

        $php .= "\n".'
		}
		';

        $php = trim($php);
        $path = base_path("app/Http/Controllers/");
        file_put_contents($path.'Api'.$controller_name.'Controller.php', $php);
    }



    /**
     * Routing for CB Namespace
     * @param $prefix
     * @param $controller
     */
    public function cbNamespaceRouteController($prefix, $controller) {
        $this->routeController($prefix, $controller, "\crocodicstudio\crudbooster\controllers");
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
    public function routeController($prefix, $controller, $namespace = null)
    {

        $prefix = trim($prefix, '/').'/';

        $namespace = ($namespace) ?: 'App\Http\Controllers';

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);

            $controller_class = new \ReflectionClass($namespace.'\\'.$controller);
            $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class != 'Illuminate\Routing\Controller' && $method->name != 'getIndex') {
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
            }
        } catch (\Exception $e) {

        }
    }
}
