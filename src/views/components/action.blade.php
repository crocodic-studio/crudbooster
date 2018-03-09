@include('crudbooster::components.action.add')

@if($button_action_style == 'button_text')
    @include('crudbooster::components.action.button_text')
@elseif($button_action_style == 'button_icon_text')
    @include('crudbooster::components.action.buttonIconText')

@elseif($button_action_style == 'dropdown')
    @include('crudbooster::components.action.dropDown')
@else
    @include('crudbooster::components.action.default')
@endif