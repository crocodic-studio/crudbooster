<?php
$labels = request('column');
$name = request('name');
$isImage = request('is_image');
$isDownload = request('is_download');
$width = request('width');
?>

        $this->col[] = [];
@foreach($labels as $i => $label)
@if(!$name[$i]) @continue @endif
        $this->col[] = [
            'label' => '{!! $label !!}',
            'name' => '{!! $name[$i] !!}',
@if ($isImage[$i])            "image" => true,
@endif
@if ($isDownload[$i])           "download" => true,
@endif
           "callback" => function($row) {},
@if ($width[$i])        'width' => '{!! $width[$i] !!}',
@endif
        ];
@endforeach