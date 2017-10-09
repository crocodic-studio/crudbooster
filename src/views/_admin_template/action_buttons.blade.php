@if(count($index_button))

    @foreach($index_button as $ib)
        <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}'
           class='btn {{($ib['color'])?'btn-'.$ib['color']:'btn-primary'}} btn-sm'
           @if($ib['onClick']) onClick='return {{$ib["onClick"]}}' @endif
           @if($ib['onMouseOever']) onMouseOever='return {{$ib["onMouseOever"]}}' @endif
           @if($ib['onMoueseOut']) onMoueseOut='return {{$ib["onMoueseOut"]}}' @endif
           @if($ib['onKeyDown']) onKeyDown='return {{$ib["onKeyDown"]}}' @endif
           @if($ib['onLoad']) onLoad='return {{$ib["onLoad"]}}' @endif
        >
            <i class='{{$ib["icon"]}}'></i>
            {{$ib["label"]}}
        </a>
    @endforeach
@endif