@include('crudbooster::components.action.add')

@if(in_array($button_action_style,['button_text', 'button_icon_text', 'dropdown']))
    @include('crudbooster::components.action.'.$button_action_style)
@else
    @include('crudbooster::components.action.default')
@endif