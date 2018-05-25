<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class ViewHelpers
{
    public static function getUrlParameters($exception = null)
    {
        @$get = $_GET;

        if (! $get) {
            return '';
        }
        if (is_array($exception)) {
            foreach ($exception as $e) {
                unset($get[$e]);
            }
        }

        $inputhtml = '';
        $string_params = http_build_query($get);
        foreach (explode('&', $string_params) as $s) {
            $part = explode('=', $s);
            $name = urldecode($part[0]);
            $value = urldecode($part[1]);
            $inputhtml .= "<input type='hidden' name='$name' value='$value'/>";
        }

        return $inputhtml;
    }
}