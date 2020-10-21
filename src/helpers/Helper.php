<?php
/* 
| ---------------------------------------------------------------------------------------------------------------
| Main Helper of CRUDBooster
| Do not edit or modify this helper unless your modification will be replace if any update from CRUDBooster.
| 
| Homepage : http://crudbooster.com
| ---------------------------------------------------------------------------------------------------------------
|
*/


if(!function_exists('ends_with')) {
    /**
     * Laravel ends_with alternative
     * @param $text
     * @param $need
     * @return bool
     */
    function ends_with($text, $need) {
        return \Illuminate\Support\Str::endsWith($text, $need);
    }
}

if(!function_exists('cbLang')) {
    /**
     * @param $key
     * @param array $replace
     * @param null $locale
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    function cbLang($key, array $replace = [], $locale = null) {
        return trans("crudbooster::crudbooster.".$key, $replace, $locale);
    }
}

if(!function_exists('db')) {
    /**
     * @param string $table
     * @return \Illuminate\Database\Query\Builder
     */
    function db(string $table) {
        return \Illuminate\Support\Facades\DB::table($table);
    }
}

if(!function_exists('assetThumbnail')) {
    function assetThumbnail($path) {
        $path = str_replace('uploads/','uploads_thumbnail/',$path);
        return asset($path);
    }
}

if(!function_exists('assetResize')) {
    function assetResize($path,$width,$height=null,$quality=70) {
        $basename = basename($path);
        $pathWithoutName = str_replace($basename, '', $path);
        $newLocation = $pathWithoutName.'/w_'.$width.'_h_'.$height.'_'.$basename;
        if(Storage::exists($newLocation)) {
            return asset($newLocation);
        }else{
            $img = Image::make(storage_path($path))->fit($width,$height);
            $img->save(storage_path($newLocation),$quality);
            return asset($newLocation);
        }
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


if(!function_exists('now')) {
    function now() {
        return date('Y-m-d H:i:s');
    }
}

/* 
| --------------------------------------------------------------------------------------------------------------
| Get data from input post/get more simply
| --------------------------------------------------------------------------------------------------------------
| $name = name of input
|
*/

if(!function_exists('get_setting')) {
    /**
     * @param $key
     * @param null $default
     * @return bool
     */
    function get_setting($key, $default = null) {
        $setting = \crocodicstudio\crudbooster\helpers\CB::getSetting($key);
        $setting = ($setting)?:$default;
        return $setting;
    }
}

if(!function_exists('str_random')) {
    function str_random($length = 16) {
        return \Illuminate\Support\Str::random($length);
    }
}

if(!function_exists('str_slug')) {
    function str_slug($text, $separator = "-", $language = "en") {
        return \Illuminate\Support\Str::slug($text, $separator, $language);
    }
}

if(!function_exists('g')) {
    /**
     * @param $key
     * @param null $default
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\Request|string
     */
    function g($key, $default = null) {
        return request($key, $default);
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

