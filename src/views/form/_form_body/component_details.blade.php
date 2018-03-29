<?php
$fileLocation = CB::componentsPath($type.'/component_detail.blade.php');
$userLocation = CB::PublishedComponentsPath($type.'/component_detail.blade.php');
?>

@if(file_exists($fileLocation))
    <?php $containTR = (substr(trim(file_get_contents($fileLocation)), 0, 4) === '<tr>')?>
    @if($containTR)
        @include('crudbooster::form.type_components.'.$type.'.component_detail')
    @else
        <tr>
            <td>{{$label}}</td>
            <td>@include('crudbooster::form.type_components.'.$type.'.component_detail')</td>
        </tr>
    @endif
@elseif(file_exists($userLocation))
    <?php $containTR = (substr(trim(file_get_contents($userLocation)), 0, 4) === '<tr>')?>
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
