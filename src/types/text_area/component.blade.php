@extends('types::layout')
@section('content')
    <?php /** @var \crocodicstudio\crudbooster\types\text\TextAreaModel $column */ ?>
    <textarea name="{{$column->getName()}}" id="{{$column->getName()}}"
              {{ $column->getRequired()?'required':''}}
              {{ $column->getReadonly()?'readonly':''}}
              placeholder="{{ $column->getPlaceholder() }}"
              {{ $column->getDisabled()?'disabled':''}}
              class='form-control'
              rows='5'>{{ $column->getValue() }}</textarea>
@endsection