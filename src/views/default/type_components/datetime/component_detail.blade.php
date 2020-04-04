@php
    $original_local = setlocale(LC_TIME,0);
    setlocale(LC_TIME,App::getLocale());
@endphp
{{ !empty($value) ? ucfirst(strftime("%B, %d %G %H:%M:%S", strtotime($value))) : null }}
@php
    setlocale(LC_TIME,$original_local);
@endphp