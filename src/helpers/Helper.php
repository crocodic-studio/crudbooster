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
if (! function_exists('cbAdminPath')) {
    function cbAdminPath()
    {
        return cbConfig('ADMIN_PATH');
    }
}

if (! function_exists('ctrlNamespace')) {
    function ctrlNamespace()
    {
        return 'App\Http\Controllers';
    }
}

if (! function_exists('is_checked')) {
    /**
     * @param $format
     * @param $value
     * @param $option_value
     * @return string
     */
    function is_checked($format, $value, $option_value)
    {
        switch ($format) {
            case 'JSON':
                $valueFormat = json_decode($value, true);
                $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                break;
            default:
            case 'COMMA_SEPARATOR':
                $valueFormat = explode(', ', $value);
                $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                break;
            case 'SEMICOLON_SEPARATOR':
                $valueFormat = explode('; ', $value);
                $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                break;
        }

        return $checked;
    }
}

if (! function_exists('parseControllerConfigToArray')) {
    function parseControllerConfigToArray($code)
    {
        return \crocodicstudio\crudbooster\helpers\Parsers\ControllerConfigParser::parse($code);
    }
}

if (! function_exists('controllers_dir')) {
    function controllers_dir()
    {
        return 'app/Http/Controllers/';
    }
}

if (! function_exists('parseScaffoldingToArray')) {
    function parseScaffoldingToArray($code, $type = 'form')
    {
        return \crocodicstudio\crudbooster\helpers\Parsers\ScaffoldingParser::parse($code, $type);
    }
}

if (! function_exists('controller_path')) {
    function controller_path($controller)
    {
        return app_path('Http/Controllers/'.$controller.'.php');
    }
}

if (! function_exists('getTablesList')) {
    function getTablesList()
    {
        $tables_list = [];
        foreach (CRUDBooster::listTables() as $tableObj) {

            $tableName = $tableObj->TABLE_NAME;
            if ($tableName == config('database.migrations')) {
                continue;
            }
            if (substr($tableName, 0, 4) == 'cms_' && $tableName != 'cms_users') {
                continue;
            }

            $tables_list[] = $tableName;
        }

        return $tables_list;
    }
}

if (! function_exists('readMethodContent')) {
    function readMethodContent($code, $findMethod)
    {
        $functionToFind = $findMethod;

        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagTutup = 0;
        $tagPembukas = [];
        $tagPentutups = [];
        $methodIndex = [];
        $indentCount = 0;
        $methodAccessCandidate = ['public', 'private'];

        $e = 0;
        foreach ($codeArray as &$line) {

            // if($line=='') continue;

            if (strpos($line, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $e;
                $indentCount = substr_count($line, "\t");
            }

            if (stripos($line, '}') !== false) {
                $tagPentutups[$e] = $e;
            }

            if (stripos($line, '{') !== false) {
                $tagPembukas[$e] = $e;
            }

            foreach ($methodAccessCandidate as $m) {
                if (strpos($line, $m) !== false) {
                    $methodIndex[] = $e;
                    break;
                }
            }

            if (strpos($line, ' function ') !== false) {
                $methodIndex[] = $e;
            }

            $e++;
        }

        $methodIndex = array_unique($methodIndex);
        $methodIndex = array_values($methodIndex); //reset keys

        $keyTagBukaInMethodIndex = array_search($tagBuka, $methodIndex);
        $totalMethodIndex = count($methodIndex) - 1;
        $methodNextIndex = ($totalMethodIndex == $keyTagBukaInMethodIndex) ? $keyTagBukaInMethodIndex : $keyTagBukaInMethodIndex + 1;

        $findIndexPenutup = 0;
        $tagPentutups = array_values($tagPentutups);

        if ($tagBuka == end($methodIndex)) {
            $finalTagPenutup = $tagPentutups[count($tagPentutups) - 2];
        } else {
            foreach ($tagPentutups as $i => $tp) {
                if ($tp > $methodIndex[$methodNextIndex]) {
                    $finalTagPenutup = $tagPentutups[$i - 1];
                    break;
                }
            }
        }

        $content = [];
        foreach ($codeArray as $i => $c) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                $content[] = $c;
            }
        }

        return implode("\n", $content);
    }
}

if (! function_exists('writeMethodContent')) {
    function writeMethodContent($code, $findMethod, $stringContent)
    {
        $functionToFind = $findMethod;
        $stringToInsert = $stringContent;

        $codeArray = explode("\n", $code);
        $tagBuka = 0;
        $tagTutup = 0;
        $tagPembukas = [];
        $tagPentutups = [];
        $methodIndex = [];
        $indentCount = 0;
        $methodAccessCandidate = ['public', 'private'];

        $e = 0;
        foreach ($codeArray as &$a) {

            // if($a=='') continue;

            if (strpos($a, 'function '.$functionToFind.'(') !== false) {
                $tagBuka = $e;
                $indentCount = substr_count($a, "\t");
            }

            if (stripos($a, '}') !== false) {
                $tagPentutups[$e] = $e;
            }

            if (stripos($a, '{') !== false) {
                $tagPembukas[$e] = $e;
            }

            foreach ($methodAccessCandidate as $m) {
                if (strpos($a, $m) !== false) {
                    $methodIndex[] = $e;
                    break;
                }
            }

            if (strpos($a, ' function ') !== false) {
                $methodIndex[] = $e;
            }

            $e++;
        }

        $methodIndex = array_unique($methodIndex);
        $methodIndex = array_values($methodIndex); //reset keys

        $keyTagBukaInMethodIndex = array_search($tagBuka, $methodIndex);
        $totalMethodIndex = count($methodIndex) - 1;
        $methodNextIndex = ($totalMethodIndex == $keyTagBukaInMethodIndex) ? $keyTagBukaInMethodIndex : $keyTagBukaInMethodIndex + 1;

        $findIndexPenutup = 0;
        $tagPentutups = array_values($tagPentutups);
        if ($tagBuka == end($methodIndex)) {
            $finalTagPenutup = $tagPentutups[count($tagPentutups) - 2];
        } else {
            foreach ($tagPentutups as $i => $tp) {
                if ($tp > $methodIndex[$methodNextIndex]) {
                    $finalTagPenutup = $tagPentutups[$i - 1];
                    break;
                }
            }
        }

        //Removing Content Of Method
        foreach ($codeArray as $i => $c) {
            if ($i > $tagBuka && $i < $finalTagPenutup) {
                unset($codeArray[$i]);
            }
        }

        //Insert Content To Method
        $stringToInsertArray = explode("\n", trim($stringToInsert));
        foreach ($stringToInsertArray as $i => &$s) {
            $s = str_repeat("\t", $indentCount + 2).trim($s);
        }

        $stringToInsert = implode("\n", $stringToInsertArray);
        foreach ($codeArray as $i => $c) {
            if ($i == $tagBuka) {
                array_splice($codeArray, $i + 1, 0, [$stringToInsert]);
            }
        }

        return implode("\n", $codeArray);
    }
}

if (! function_exists('rrmdir')) {
    function rrmdir($dir)
    {
        if (! is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == "..") {
                continue;
            }

            $objPath = $dir."/".$object;

            if (is_dir($objPath)) {
                rrmdir($objPath);
            } else {
                unlink($objPath);
            }
        }
        rmdir($dir);
    }
}

if (! function_exists('extract_unit')) {
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

if (! function_exists('now')) {
    function now()
    {
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
if (! function_exists('g')) {
    function g($name)
    {
        return Request::get($name);
    }
}

if (! function_exists('min_var_export')) {
    function min_var_export($input, $indent = "")
    {
        if (! is_array($input)) {
            return var_export($input, true);
        }
        $buffer = [];
        foreach ($input as $key => $value) {
            $buffer[] = $indent.var_export($key, true)."=>".min_var_export($value, ($indent."\t"));
        }
        if (count($buffer) == 0) {
            return "[]";
        }

        return "[\n".implode(",\n", $buffer)."\n$indent]";
    }
}

if (! function_exists('rrmdir')) {
    /*
    * http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
    */
    function rrmdir($dir)
    {
        if (! is_dir($dir)) {
            return;
        }
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == "." || $object == "..") {
                continue;
            }
            if (is_dir($dir."/".$object)) {
                rrmdir($dir."/".$object);
            } else {
                unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}

if (! function_exists('cbTrans')) {
    function cbTrans($key, $params = [])
    {
        return trans('crudbooster.'.$key, $params);
    }
}

if (! function_exists('cbAsset')) {
    function cbAsset($key)
    {
        return asset('vendor/crudbooster/assets/'.$key);
    }
}

if (! function_exists('cbScript')) {
    function cbScript($key)
    {
        return '<script src="'.cbAsset($key).'" type="text/javascript"></script>';
    }
}

if (! function_exists('cbStyleSheet')) {
    function cbStyleSheet($key)
    {
        return '<link rel="stylesheet" type="text/css" href="'.cbAsset($key).'"/>';
    }
}

if (! function_exists('cbConfig')) {
    function cbConfig($key, $default = null)
    {
        return config('crudbooster.'.$key, $default);
    }
}
if (! function_exists('makeValidationForHTML')) {
    function makeValidationForHTML($rules)
    {
        $validation = [];
        $validation_raw = $rules ? explode('|', $rules) : [];
        foreach ($validation_raw as $vr) {
            $vr_a = explode(':', $vr);
            if ($vr_a[1]) {
                $validation[$vr_a[0]] = $vr_a[1];
            } else {
                $validation[$vr] = true;
            }
        }

        return $validation;
    }
}

if (! function_exists('findSelected')) {
    /**
     * @param $rawvalue
     * @param $form
     * @param $option_value
     * @param $value
     * @return string
     */
    function findSelected($rawvalue, $form, $option_value)
    {
        $value = $rawvalue;
        if (! $rawvalue) {
            return '';
        }

        if ($form['options']['multiple'] !== true) {
            return ($option_value == $value) ? "selected" : "";
        }

        switch ($form['options']['multiple_result_format']) {
            case 'JSON':
                $value = json_decode($rawvalue, true) ?: [];
                $selected = (in_array($option_value, $value)) ? "selected" : "";
                break;
            default:
            case 'COMMA_SEPARATOR':
                $value = explode(', ', $rawvalue);
                $selected = (in_array($option_value, $value)) ? "selected" : "";
                break;
            case 'SEMICOLON_SEPARATOR':
                $value = explode('; ', $rawvalue);
                $selected = (in_array($option_value, $value)) ? "selected" : "";
                break;
        }
        return $selected;
    }
}
if (! function_exists('array_get_keys')) {

    /**
     * @param array $_array
     * @param array $keys
     * @param null $default
     * @return string
     */
    function array_get_keys(array $_array, array $keys, $default = null)
    {
        $_defaults = array_fill_keys($keys, $default);
        return array_merge($_defaults, array_intersect_key($_array, $_defaults));
    }
}
