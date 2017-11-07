<?php

if (strpos($dataEnum, ';') !== false) {
    $dataEnum = explode(";", $dataEnum);
} else {
    $dataEnum = [$dataEnum];
}
array_walk($dataEnum, 'trim');

$radios = [];
foreach ($dataEnum as $e=>$enum){
    $enum = explode('|', $enum);
    if (count($enum) == 1) {
        $enum[1] = $enum[0];
    }
    $radios[$e]['value'] = $enum[0];
    $radios[$e]['label'] = $enum[1];
}
?>

@foreach($radios as $e=>$enum)
    <label class="radio-inline">
        <input type="radio" name="child-{{$col['name']}}"
               class='{{ ($e==0 && $col['required'])?"required":""}} {{$name_column}}'
               value="{{$enum['value']}}"> {{$enum['label']}}
    </label>
@endforeach
