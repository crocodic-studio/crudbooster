<?php
$imagesExtension = explode(',', cbConfig('IMAGE_EXTENSIONS'));
$ext = pathinfo($value, PATHINFO_EXTENSION);
?>
@if(Storage::exists($value))
    @if(in_array($ext, $imagesExtension))
        <a data-lightbox='roadtrip' href='{{asset($value)}}'>
            <img style='max-width:150px' title="Image For {{$label}}" src='{{asset($value)}}'/>
        </a>
    @else
        <a href='{{asset($value)}}?download=1'
           title="File For {{$label}}"
           target="_blank">{{cbTrans("button_download_file")}}
        </a>
    @endif
@endif