@php
    $defaults = [
    'type' => 'text',
    'required' => '',
    'readonly' => '',
    'disabled' => '',
    'help' => '',
    'value' => '',
    'validation' => [],
    'width' => 'col-sm-9'
     ];

    $type = array_merge($defaults, $formInput)['type'];
@endphp

@if(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/component.blade.php')))
    @include('crudbooster::default.type_components.'.$type.'.component')
@elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/component.blade.php')))
    @include('vendor.crudbooster.type_components.'.$type.'.component')
@else
    <p class='text-danger'>{{$type}} is not found in type component system</p><br/>
@endif