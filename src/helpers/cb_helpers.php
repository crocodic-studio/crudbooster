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
    function cbModulesNS(string $path = ''): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::cbModulesNS($path);
    }
}

if (! function_exists('cbStartMarker')) {
    function cbStartMarker(string $section): string
    {
        return "# START $section DO NOT REMOVE THIS LINE";
    }
}

if (! function_exists('cbEndMarker')) {
    function cbEndMarker(string $section): string
    {
        return "# END $section DO NOT REMOVE THIS LINE";
    }
}

if (! function_exists('cbAdminPath')) {
    function cbAdminPath(): string
    {
        return cbConfig('ADMIN_PATH');
    }
}

if (! function_exists('ctrlNamespace')) {
    function ctrlNamespace(): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::ctrlNamespace();
    }
}

if (! function_exists('cbControllersNS')) {
    function cbControllersNS(): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::cbControllersNS();
    }
}

if (! function_exists('is_checked')) {
    /**
     * @param $format
     * @param $value
     * @param $option_value
     * @return string
     */
    function is_checked(string $format, $value, $option_value): string
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

if (! function_exists('CbComponentsPath')) {
    function CbComponentsPath(string $type = ''): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::componentsPath($type);
    }
}

if (! function_exists('CbPublishedComponentsPath')) {
    function CbPublishedComponentsPath(string $type = ''): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::publishedComponentsPath($type);
    }
}

if (! function_exists('controllers_dir')) {
    function controllers_dir(): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::controllersDir();
    }
}

if (! function_exists('controller_path')) {
    function controller_path(string $controller): string
    {
        return \Crocodicstudio\Crudbooster\Helpers\CbStructure::controllerPath($controller);
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

if (! function_exists('cbTrans')) {
    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array $replace
     * @return string
     */
    function cbTrans($key, $replace = [])
    {
        return trans('crudbooster.'.$key, $replace);
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
        $validation_raw = is_string($rules)? explode('|', $rules) : $rules;
        foreach ($validation_raw as $vr) {
            $vr_a = explode(':', $vr);
            if (isset($vr_a[1])) {
                $validation[$vr_a[0]] = $vr_a[1];
            } else {
                $validation[$vr] = true;
            }
        }

        return $validation + ['max' => ''];
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
     * @param mixed $default
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
        return \Crocodicstudio\Crudbooster\Modules\SettingModule\SettingRepo::getSetting($name);
    }
}

if (! function_exists('backWithMsg')) {
    function backWithMsg($msg, $type = 'success')
    {
        respondWith(redirect()->back()->with(['message' => $msg, 'message_type' => $type,]));
    }
}

if (! function_exists('underField')) {
    function underField($help, $error)
    {
        $error = $error ? "<i class='fa fa-info-circle'></i> $error" : '';

        return "<div class='text-danger'>$error</div><p class='help-block'>$help</p>";
    }
}
if (! function_exists('cbIcon')) {
    function cbIcon($icon)
    {
        return '<i class=\'fa fa-'.$icon.'\'></i>';
    }
}

if (! function_exists('YmdHis')) {
    function YmdHis()
    {
        return date('Y-m-d H:i:s');
    }
}

if (! function_exists('cbUser')) {
    function cbUser()
    {
        return auth('cbAdmin')->user();
    }
}