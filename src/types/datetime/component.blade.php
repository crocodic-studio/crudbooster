@extends('types::layout')
@section('content')
    @php /** @var \crocodicstudio\crudbooster\types\datetime\DatetimeModel $column */  @endphp
        <input type='text' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               class='form-control datetime-picker'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               value='{{ $column->getValue() }}'/>
@endsection