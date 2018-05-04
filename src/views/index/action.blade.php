@include('crudbooster::index.action.add')

@if(in_array($buttonActionStyle,['button_text', 'button_icon_text', 'dropdown']))
    @include('crudbooster::index.action.'.$buttonActionStyle)
@else
    @include('crudbooster::index.action.default')
@endif