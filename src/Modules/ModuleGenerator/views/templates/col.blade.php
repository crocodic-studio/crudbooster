<?php
$labels = request('column');
$name = request('name');
$isImage = request('is_image');
$isDownload = request('is_download');
$callback = request('callback');
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
@if ($callback[$i])            "callback" => function($row) {!! $callback[$i] !!}},
@endif
@if ($width[$i])        'width' => '{!! $width[$i] !!}',
@endif
        ];
@endforeach