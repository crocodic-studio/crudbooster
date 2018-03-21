<?php
$file_location = CB::componentsPath($type).'/component_detail.blade.php';
$user_location = CB::PublishedComponentsPath($type).'/component_detail.blade.php');
?>

@if(file_exists($file_location))
    <?php $containTR = (substr(trim(file_get_contents($file_location)), 0, 4) == '<tr>') ? TRUE : FALSE;?>
    @if($containTR)
        @include('crudbooster::default.type_components.'.$type.'.component_detail')
    @else
        <tr>
            <td>{{$label}}</td>
            <td>@include('crudbooster::default.type_components.'.$type.'.component_detail')</td>
        </tr>
    @endif
@elseif(file_exists($user_location))
    <?php $containTR = (substr(trim(file_get_contents($user_location)), 0, 4) == '<tr>') ? TRUE : FALSE;?>
    @if($containTR)
        @include('vendor.crudbooster.type_components.'.$type.'.component_detail')
    @else
        <tr>
            <td>{{$label}}</td>
            <td>@include('vendor.crudbooster.type_components.'.$type.'.component_detail')</td>
        </tr>
    @endif
@else
    <!-- <tr><td colspan='2'>NO COMPONENT {{$type}}</td></tr> -->
@endif
