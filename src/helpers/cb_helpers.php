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
if (! function_exists('cbModulesNS')) {
    function cbModulesNS($path = '')
    {
        return '\crocodicstudio\crudbooster\Modules\\'.$path;
    }
}

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
        if ($format == 'JSON') {
            $valueFormat = json_decode($value, true);
        } elseif ($format == 'COMMA_SEPARATOR') {
            $valueFormat = explode(', ', $value);
        } elseif ($format == 'SEMICOLON_SEPARATOR') {
            $valueFormat = explode('; ', $value);
        } else {
            $valueFormat = [];
        }
        $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";

        return $checked;
    }
}

if (! function_exists('controllers_dir')) {
    function controllers_dir()
    {
        $_ = DIRECTORY_SEPARATOR;
        return 'app'.$_.'Http'.$_.'Controllers'.$_;
    }
}

if (! function_exists('controller_path')) {
    function controller_path($controller)
    {
        $_ = DIRECTORY_SEPARATOR;
        return app_path('Http'.$_.'Controllers'.$_.$controller.'.php');
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
     * @param $optionValue
     * @return string
     */
    function findSelected($rawvalue, $form, $optionValue)
    {
        if (! $rawvalue) {
            return '';
        }
        $value = $rawvalue;

        if ($form['options']['multiple'] !== true) {
            return ($optionValue == $value) ? "selected" : "";
        }

        $val = $form['options']['multiple_result_format'];
        if ($val == 'JSON') {
            $selected = (json_decode($rawvalue, true) ?: []);
        } elseif ($val == 'SEMICOLON_SEPARATOR') {
            $selected = explode('; ', $rawvalue);
        } else {
            $selected = explode(', ', $rawvalue);
        }
        in_array($optionValue, $selected) ? "selected" : "";

        return $selected;
    }
}
if (! function_exists('array_get_keys')) {

    /**
     * @param array $_array
     * @param array $keys
     * @param null $default
     * @return array
     */
    function array_get_keys(array $_array, array $keys, $default = null)
    {
        $_defaults = array_fill_keys($keys, $default);

        return array_merge($_defaults, array_intersect_key($_array, $_defaults));
    }
}

if (! function_exists('cbGetSetting')) {
    function cbGetSetting($name)
    {
        return \crocodicstudio\crudbooster\Modules\SettingModule\SettingRepo::getSetting($name);
    }
}

if (! function_exists('backWithMsg')) {
    function backWithMsg($msg, $type = 'success')
    {
        sendAndTerminate(redirect()->back()->with(['message_type' => $type, 'message' => $msg]));
    }
}

if (! function_exists('underField')) {
    function underField($help, $error)
    {
        $error = $error ? "<i class='fa fa-info-circle'></i> $error":'' ;
        return "<div class='text-danger'>$error</div><p class='help-block'>$help</p>";
    }
}
