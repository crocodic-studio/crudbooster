@extends('types::layout')
@section('content')
    @php /** @var \crocodicstudio\crudbooster\types\date\DateModel $column */ @endphp
        <input type='text' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               class='form-control date-picker'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               value='{{ $column->getValue() }}'/>
@endsection