<?php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */ ?>
<div class='form-group {{ ($errors->first( $column->getName() ))?"has-error":"" }}' id='form-group-{{ $column->getName() }}'>
    <label>
        {{ $column->getLabel() }}
        @if($column->getRequired())
            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="row">
        <div class="col-sm-{{ $column->getInputWidth() }}">