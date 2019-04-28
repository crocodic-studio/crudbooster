@extends('types::layout')
@section('content')
    <?php /** @var \crocodicstudio\crudbooster\types\select\SelectModel $column */?>
    <select style="width: 100%" class="form-control select2"
            {{ $column->getRequired()?'required':''}}
            {{ $column->getReadonly()?'readonly':''}}
            {{ $column->getDisabled()?'disabled':''}}
            name="{{ $column->getName() }}" id="{{ $column->getName() }}">
        <option value="">** Select a {{ $column->getLabel() }}</option>
        @foreach($column->getOptions() as $key=>$value)
            <option {{ $column->getValue()==$key?'selected':'' }} value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
@endsection