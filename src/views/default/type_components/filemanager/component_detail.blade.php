@if($value)
    @if($form['filemanager_type'] == 'file')
        <a target="_blank" href="{{asset($value)}}">
            {{ basename($value) }}
        </a>
    @else
        <a data-lightbox="roadtrip" href="{{asset($value)}}">
            <img src="{{asset($value)}}" style="max-width: 150px">
        </a>
    @endif
@endif