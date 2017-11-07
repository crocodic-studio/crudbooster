<?php
if(@$formInput['options']['query']){
    $field_label = $formInput['options']['field_label'];
    $field_value = $formInput['options']['field_value'];

    $selects_data = DB::select(DB::raw($formInput['options']['query']));
}
?>
<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

       @if (@$formInput['options']['query'])
            @foreach ($selects_data as $d)
                <div data-val='{!! $val !!}' class='checkbox  {{ $disabled }}'>
                <label> <input type='checkbox'
                  {{ $disabled }}
                {{ is_checked($formInput['options']['result_format'], $value, $d->field_value) }}
                 name='{!! $name !!}[]'
                 value='{!! $d->$field_value !!}'>
                {!! $d->$field_label !!}
                </label> </div>
            @endforeach
        @endif
       @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>