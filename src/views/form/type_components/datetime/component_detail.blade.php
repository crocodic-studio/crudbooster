@php
    $format = ($formInput['options']['php_format'])?:"Y-m-d H:i";
    echo date($format,strtotime($value));
@endphp