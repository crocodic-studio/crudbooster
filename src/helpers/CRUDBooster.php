<?php

namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\CBCoreModule\RouteController;
use crocodicstudio\crudbooster\CBCoreModule\ViewHelpers;
use crocodicstudio\crudbooster\Modules\LogsModule\LogsRepository;
use crocodicstudio\crudbooster\Modules\PrivilegeModule\PrivilegeHelpers;
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
    use PrivilegeHelpers;

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
        return CbUsersRepo::find(session('admin_id'));
    }

    public static function myName()
    {
        return session('admin_name');
    }

    public static function myPhoto()
    {
        return session('admin_photo');
    }

    public static function isLocked()
    {
        return session('admin_lock');
    }

    public static function getCurrentModule()
    {
        return GetCurrentX::getCurrentModule();
    }

    public static function getCurrentMenuId()
    {
        return GetCurrentX::getCurrentMenuId();
    }

    public static function adminPath($path = null)
    {
        return url(cbAdminPath().'/'.$path);
    }

    public static function deleteConfirm($redirectTo)
    {
        ViewHelpers::delConfirm($redirectTo);
    }

    public static function getCurrentId()
    {
        return GetCurrentX::getCurrentId();
    }

    public static function getCurrentMethod()
    {
        return GetCurrentX::getCurrentMethod();
    }

    public static function getValueFilter($field)
    {
        self::getFilter($field, 'value');
    }

    private static function getFilter($field, $index)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field][$index];
        }
    }

    public static function getSortingFilter($field)
    {
        return self::getFilter($field, 'sorting');
    }

    public static function getTypeFilter($field)
    {
        return self::getFilter($field, 'type');
    }

/*    public static function stringBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }*/

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
        return DbInspector::findPK($table);
    }

    public static function valid($rules = [], $type = 'json')
    {
        $validator = Validator::make(request()->all(), $rules);

        if (!$validator->fails()) {
            return true;
        }

        $message = $validator->errors()->all();

        if ($type == 'json') {
            $result = [];
            $result['api_status'] = 0;
            $result['api_message'] = implode(', ', $message);
            sendAndTerminate(response()->json($result, 200));
        }

        $res = redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput();
        sendAndTerminate($res);
    }

    public static function getTableForeignKey($fieldName)
    {
        if (substr($fieldName, 0, 3) == 'id_' || substr($fieldName, -3) == '_id') {
            return str_replace(['_id', 'id_'], '', $fieldName);
        }
    }

    public static function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        return \crocodicstudio\crudbooster\CBCoreModule\Index\ViewHelpers::urlFilterColumn($key, $type, $value, $singleSorting);
    }
    public static function mainpath($path = null)
    {
        $controllerName = strtok(Route::currentRouteAction(), '@');
        // $controllerName = array_pop(explode('\\', $controllerName));
        $controllerName = basename($controllerName);
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
        LogsRepository::insertLog('crudbooster: '.$description, self::myId());
    }

    public static function insertTryLog($action, $name = '')
    {
        self::insertLog(trans("logging.log_try_".$action, ['name' => $name, 'module' => self::getCurrentModule()]));
    }

    public static function myId()
    {
        return session('admin_id');
    }

    public static function referer()
    {
        return Request::server('HTTP_REFERER');
    }

    public static function listCbTables()
    {
        $tablesList = [];
        foreach (DbInspector::listTables() as $tableObj) {

            $tableName = $tableObj->TABLE_NAME;
            if ($tableName == config('database.migrations')) {
                continue;
            }
            if (substr($tableName, 0, 4) == 'cms_' && $tableName != 'cms_users') {
                continue;
            }

            $tablesList[] = $tableName;
        }

        return $tablesList;
    }

    public static function getUrlParameters($exception = null)
    {
        return ViewHelpers::getUrlParameters($exception);
    }

/*    public static function isExistsController($table)
    {
        $ctrlName = ucwords(str_replace('_', ' ', $table));
        $ctrlName = str_replace(' ', '', $ctrlName).'Controller.php';
        $path = base_path(controllers_dir());
        $path2 = base_path(controllers_dir()."ControllerMaster/");

        if (file_exists($path.'Admin'.$ctrlName) || file_exists($path2.'Admin'.$ctrlName) || file_exists($path2.$ctrlName)) {
            return true;
        }

        return false;
    }*/

    public static function routeController($prefix, $controller, $namespace = null)
    {
        RouteController::routeController($prefix, $controller, $namespace);
    }

    /*
    | -------------------------------------------------------------
    | Alternate route for Laravel Route::controller
    | -------------------------------------------------------------
    | $prefix       = path of route
    | $controller   = controller name
    | $namespace    = namespace of controller (optional)
    |
    */

    public static function denyAccess()
    {
        static::redirect(static::adminPath(), cbTrans('denied_access'));
    }

    public static function redirect($to, $message, $type = 'warning')
    {
        if (Request::ajax()) {
            sendAndTerminate(response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $to]));
        }

        sendAndTerminate(redirect($to)->with(['message' => $message, 'message_type' => $type]));
    }

    public static function componentsPath($type = '')
    {
        $componentPath = implode(DIRECTORY_SEPARATOR, ['vendor', 'crocodicstudio', 'crudbooster', 'src', 'views', 'default', 'type_components', $type]);
        return base_path($componentPath);

    }

    public static function PublishedComponentsPath($type = '')
    {
        $Path = implode(DIRECTORY_SEPARATOR, ['views', 'vendor', 'crudbooster', 'type_components', $type]);
        return resource_path($Path);
    }
}
