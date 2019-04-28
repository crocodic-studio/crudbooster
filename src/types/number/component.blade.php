@extends('types::layout')
@section('content')
    @php /** @var \crocodicstudio\crudbooster\types\number\NumberModel $column */  @endphp
        <input type='number' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               max="{{ $column->getMax() }}"
               min="{{ $column->getMin() }}"
               class='form-control'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               value='{{ $column->getValue() }}'/>
@endsection