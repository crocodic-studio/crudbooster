@if(!empty($indexButton))
    @foreach($indexButton as $ib)
        <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}'
           class='btn {{($ib['color'])?'btn-'.$ib['color']:'btn-primary'}} btn-sm'
           @if(isset($ib['onClick'])) onClick='return {{$ib["onClick"]}}' @endif
           @if(isset($ib['onMouseOver'])) onMouseOver='return {{$ib["onMouseOver"]}}' @endif
           @if(isset($ib['onMouseOut'])) onMouseOut='return {{$ib["onMouseOut"]}}' @endif
           @if(isset($ib['onKeyDown'])) onKeyDown='return {{$ib["onKeyDown"]}}' @endif
           @if(isset($ib['onLoad'])) onLoad='return {{$ib["onLoad"]}}' @endif
        >
            <i class='{{$ib["icon"]}}'></i>
            {{$ib["label"]}}
        </a>
    @endforeach
@endif