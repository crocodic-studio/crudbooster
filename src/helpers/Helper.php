<?php

/*
 * Theme Helper
 */
if(!function_exists("themeLoginBackground")) {
    function themeLoginBackground() {
        return "crudbooster::layouts.login_background_css";
    }
}
if(!function_exists("themeFlashMessageAlert")) {
    function themeFlashMessageAlert() {
        return "crudbooster::layouts.flash_message_alert";
    }
}

if(!function_exists("themeFlashMessage")) {
    function themeFlashMessage() {
        return "crudbooster::layouts.flash_message";
    }
}

if(!function_exists("themeAlertMessage")) {
    function themeAlertMessage() {
        return "crudbooster::layouts.alert_message";
    }
}

if(!function_exists("themeLayoutPageTitleAndButton")) {
    function themeLayoutPageTitleAndButton() {
        return 'crudbooster::module.index.index_head_buttons';
    }
}

if(!function_exists("themeLayoutHead")) {
    function themeLayoutHead() {
        return "crudbooster::layouts.head";
    }
}
if(!function_exists("themeTitle")) {
    function themeTitle($page_title) {
        $page_title = isset($page_title)?$page_title:module()->getPageTitle();
        return (isset($page_title))?cb()->getAppName().': '.strip_tags($page_title):"Admin Area";
    }
}

if(!function_exists("getThemePath")) {
    function getThemePath($path = null) {
        return getSetting("theme_path", "crudbooster::themes.adminlte").(($path)?".".$path:null);
    }
}

// End Theme Helper

if(!function_exists("isConfigCached")) {
    function isConfigCached()
    {
        if(env("APP_NAME")=="" || env("APP_NAME")==null) return true;
        else return false;
    }
}

if(!function_exists("miscellanousSingleton")) {
    /**
     * @return \crocodicstudio\crudbooster\helpers\MiscellanousSingleton
     */
    function miscellanousSingleton() {
        return app("MiscellanousSingleton");
    }
}

if(!function_exists("makeReferalUrl")) {
    function makeReferalUrl($name = null) {
        $ref = [];
        $ref['url'] = request()->fullUrl();
        $ref['name'] = $name;
        $md5Hash = md5(serialize($ref));

        if($exist = \Illuminate\Support\Facades\Cache::get("refurl_".$md5Hash)) {
            return $exist['id'];
        }

        $ref['id'] = $newID = \Illuminate\Support\Str::random(7);

        \Illuminate\Support\Facades\Cache::forever("refurl_token_".$newID, $md5Hash);
        \Illuminate\Support\Facades\Cache::forever("refurl_".$md5Hash, $ref);
        return $newID;
    }
}

if(!function_exists("getReferalUrl")) {
    function getReferalUrl($key = null) {
        if(verifyReferalUrl()) {
            $md5hash = \Illuminate\Support\Facades\Cache::get("refurl_token_".request("ref"));
            $ref = \Illuminate\Support\Facades\Cache::get("refurl_".$md5hash);
            if($key) {
                return @$ref[$key];
            } else {
                return $ref;
            }
        }
        return null;
    }
}

if(!function_exists("verifyReferalUrl")) {
    function verifyReferalUrl()
    {
        if(request("ref")) {
            if($md5hash = \Illuminate\Support\Facades\Cache::get("refurl_token_".request("ref"))) {
                $ref = \Illuminate\Support\Facades\Cache::get("refurl_".$md5hash);
                if(filter_var($ref['url'], FILTER_VALIDATE_URL)) {
                    return true;
                }
            }
        }
        return false;
    }
}

if(!function_exists("putHtaccess")) {
    function putHtaccess($stringToPut)
    {
        file_put_contents(base_path(".htaccess"), "\n".$stringToPut, FILE_APPEND);
        file_put_contents(public_path(".htaccess"), "\n".$stringToPut, FILE_APPEND);
    }
}

if(!function_exists("checkHtaccess")) {
    function checkHtaccess($stringToCheck)
    {
        if(file_exists(base_path(".htaccess")) && file_exists(public_path(".htaccess"))) {
            $htaccess = file_get_contents(base_path(".htaccess"));
            $htaccess2= file_get_contents(public_path(".htaccess"));
            if(\Illuminate\Support\Str::contains($htaccess, $stringToCheck) && \Illuminate\Support\Str::contains($htaccess2, $stringToCheck)) {
                return true;
            }
        }

        return false;
    }
}

if(!function_exists("setEnvironmentValue")) {
    function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }

            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;
    }
}


if(!function_exists("cbLang")) {
    /**
     * @param string $key
     * @param array $replace
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function cbLang($key, $replace = [])
    {
        return trans("cb::cb.".$key, $replace);
    }
}

if(!function_exists('rglob')) {
    function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

if(!function_exists('convertPHPToMomentFormat')) {
    function convertPHPToMomentFormat($format)
    {
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '', // no equivalent
            'L' => '', // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '', // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz', // deprecated since version 1.6.0 of moment.js
            'I' => '', // no equivalent
            'O' => '', // no equivalent
            'P' => '', // no equivalent
            'T' => '', // no equivalent
            'Z' => '', // no equivalent
            'c' => '', // no equivalent
            'r' => '', // no equivalent
            'U' => 'X',
        ];
        $momentFormat = strtr($format, $replacements);
        return $momentFormat;
    }
}

if(!function_exists('slug')) {
    function slug($string, $separator = '-') {
        return \Illuminate\Support\Str::slug($string, $separator);
    }
}

if(!function_exists('columnSingleton')) {
    /**
     * @return \crocodicstudio\crudbooster\controllers\scaffolding\singletons\ColumnSingleton
     */
    function columnSingleton() {
        return app('ColumnSingleton');
    }
}

if(!function_exists('cbHook'))
{
    /**
     * @return crocodicstudio\crudbooster\hooks\CBHook
     */
    function cbHook()
    {
        $className = "\App\Http\CBHook";
        return new $className();
    }
}

if(!function_exists('getTypeHook'))
{
    /**
     * @param string $type
     * @return crocodicstudio\crudbooster\types\TypesHookInterface
     */
    function getTypeHook($type) {
        $className = '\crocodicstudio\crudbooster\types\\'.$type.'\Hook';
        $typeClass = new $className();
        return $typeClass;
    }
}

if(!function_exists('getPrimaryKey'))
{
    function getPrimaryKey($table_name)
    {
        return cb()->pk($table_name);
    }
}

if(!function_exists('cb'))
{
    function cb()
    {
        return new \crocodicstudio\crudbooster\helpers\CB();
    }
}

if(!function_exists("cbAssetPath")) {
    function cbAssetPath($path = null) {
        return "cb_asset".(($path)?"/".$path:null);
    }
}
if(!function_exists('cbAsset')) {
    function cbAsset($path, $secure = null) {
        return asset(cbAssetPath()."/".$path, $secure);
    }
}

if(!function_exists("cbConfig")) {
    function cbConfig($name, $default = null) {
        return config("crudbooster.".$name, $default);
    }
}

if(!function_exists("strRandom")) {
    function strRandom($length = 5) {
        return \Illuminate\Support\Str::random($length);
    }
}

if(!function_exists('module')) {
    function module()
    {
        $module = new \crocodicstudio\crudbooster\helpers\Module();
        return $module;
    }
}

if(!function_exists('getAdminLoginURL')) {
    function getAdminLoginURL()
    {
        return cb()->getAdminUrl("login");
    }
}

if(!function_exists('dummyPhoto')) {
    function dummyPhoto()
    {
        return getSetting("dummy_photo","cb_asset/images/user8-128x128.jpg");
    }
}

if(!function_exists("str_limit")) {
    function str_limit($string, $limit = 100, $end = "...") {
        return \Illuminate\Support\Str::limit($string, $limit, $end);
    }
}

if(!function_exists('extract_unit')) {	
	/*
	Credits: Bit Repository
	URL: http://www.bitrepository.com/extract-content-between-two-delimiters-with-php.html
	*/
	function extract_unit($string, $start, $end)
	{
	$pos = stripos($string, $start);
	$str = substr($string, $pos);
	$str_two = substr($str, strlen($start));
	$second_pos = stripos($str_two, $end);
	$str_three = substr($str_two, 0, $second_pos);
	$unit = trim($str_three); // remove whitespaces
	return $unit;
	}
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/

if(!function_exists('putSetting')) {
    function putSetting($key, $value)
    {
        if(file_exists(storage_path('.cbconfig'))) {
            $settings = file_get_contents(storage_path('.cbconfig'));
            $settings = decrypt($settings);
            $settings = unserialize($settings);
        }else{
            $settings = [];
        }

        $settings[$key] = $value;

        \Illuminate\Support\Facades\Cache::forget("setting_".$key);

        $settings = serialize($settings);
        $settings = encrypt($settings);
        file_put_contents(storage_path('.cbconfig'), $settings);
    }
}

if(!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        if($cache = \Illuminate\Support\Facades\Cache::get("setting_".$key)) {
            return $cache;
        }

        if(file_exists(storage_path('.cbconfig'))) {
            $settings = file_get_contents(storage_path('.cbconfig'));
            $settings = decrypt($settings);
            $settings = unserialize($settings);
        }else{
            $settings = [];
        }

        if(isset($settings[$key])) {
            \Illuminate\Support\Facades\Cache::forever("setting_".$key, $settings[$key]);
            return $settings[$key]?:$default;
        }else{
            return $default;
        }
    }
}

if(!function_exists('timeAgo')) {
    function timeAgo($datetime_to, $datetime_from = null, $full = false)
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
}

if(!function_exists("array_map_r")) {
    function array_map_r( $func, $arr )
    {
        $newArr = array();

        foreach( $arr as $key => $value )
        {
            $key = $func($key);
            $newArr[ $key ] = ( is_array( $value ) ? array_map_r( $func, $value ) : ( is_array($func) ? call_user_func_array($func, $value) : $func( $value ) ) );
        }

        return $newArr;
    }
}

if(!function_exists("sanitizeXSS"))
{
    function sanitizeXSS($value)
    {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);
        $value = preg_replace('~\r\n?~', "\n", $value);
        return $value;
    }
}

if(!function_exists("requestAll")) {
    function requestAll() {
        $all = array_map_r("sanitizeXSS", request()->all());
        return $all;
    }
}



if(!function_exists('getURLFormat')) {
    function getURLFormat($name) {
        $url = request($name);
        if(filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }else{
            return request()->url();
        }
    }
}

if(!function_exists('g')) {
    function g($name, $safe = true) {
        if($safe == true) {
            $response = request($name);
            if(is_string($response)) {
                $response = sanitizeXSS($response);
            }elseif (is_array($response)) {
                array_walk_recursive($response, function(&$response) {
                    $response = sanitizeXSS($response);
                });
            }

            return $response;
        }else{
            return Request::get($name);
        }
    }
}

if(!function_exists('min_var_export')) {
    function min_var_export($input) {
        if(is_array($input)) {
            $buffer = [];
            foreach($input as $key => $value)
                $buffer[] = var_export($key, true)."=>".min_var_export($value);
            return "[".implode(",",$buffer)."]";
        } else
            return var_export($input, true);
    }
}

if(!function_exists('rrmdir')) {
	/*
	* http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
	*/
	function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (is_dir($dir."/".$object))
	           rrmdir($dir."/".$object);
	         else
	           unlink($dir."/".$object); 
	       } 
	     }
	     rmdir($dir); 
	   } 
	 }
}

