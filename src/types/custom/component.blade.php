@extends('types::layout')
@section('content')
        <?php /** @var \crocodicstudio\crudbooster\types\custom\CustomModel $column */?>
        {!! $column->getHtml() !!}
@endsection