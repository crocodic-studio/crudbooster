<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class ViewHelpers
{
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
        foreach (explode('&', $string_parameters) as $s) {
            $part = explode('=', $s);
            $name = urldecode($part[0]);
            $value = urldecode($part[1]);
            $inputhtml .= "<input type='hidden' name='$name' value='$value'/>";
        }

        return $inputhtml;
    }

    public static function delConfirm($redirectTo)
    {
        return 'swal({   
				title: "'.cbTrans('delete_title_confirm').'",   
				text: "'.cbTrans('delete_description_confirm').'",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#ff0000",   
				confirmButtonText: "'.cbTrans('confirmation_yes').'",  
				cancelButtonText: "'.cbTrans('confirmation_no').'",  
				closeOnConfirm: false }, 
				function(){  location.href="'.$redirectTo.'" });';
    }
}