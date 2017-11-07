@php
    $defaults = [
    'type' => 'text',
    'required' => '',
    'readonly' => '',
    'help' => '',
    'disabled' => '',
    'value' => '',
    'validation' => [],
    'width' => 'col-sm-9'
     ];

    foreach($forms as $index => $form) {
        $forms[$index] = array_merge($defaults, $form);
    }
    unset($form);

    $types =  array_column($forms,'type');
@endphp

@foreach($types as $type)
    @if(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/asset.blade.php')))
        @include('crudbooster::default.type_components.'.$type.'.asset')
    @elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/asset.blade.php')))
        @include('vendor.crudbooster.type_components.'.$type.'.asset')
    @endif
@endforeach