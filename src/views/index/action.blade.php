@include('crudbooster::index.action.add')

@if(in_array($button_action_style,['button_text', 'button_icon_text', 'dropdown']))
    @include('crudbooster::index.action.'.$button_action_style)
@else
    @include('crudbooster::index.action.default')
@endif