<?php
if (! $formInput['options']['parent_select']) {

    if (@$formInput['options']['table']):

        $table = $formInput['options']['table'];
        $field_label = $formInput['options']['field_label'];
        $field_value = $formInput['options']['field_value'];

        $selects_data = DB::table($table);

        if (\Schema::hasColumn($table, 'deleted_at')) {
            $selects_data->where($table.'.deleted_at', NULL);
        }

        if (@$formInput['options']['sql_where']) {
            $selects_data->whereraw($formInput['options']['sql_where']);
        }

        if ($formInput['options']['sql_orderby']) {
            $selects_data = $selects_data->orderByRaw($formInput['options']['sql_orderby'])->get();
        } else {
            $selects_data = $selects_data->orderby($field_value, "desc")->get();
        }

        $options = [];
        foreach ($selects_data as $i => $d) {
            $options[$i]['select'] = ($value == $d->{$field_value}) ? "selected" : "";

            if ($formInput['options']['format']) {
                $options[$i]['label'] = $formInput['options']['format'];
                foreach ($d as $k => $v) {
                    $options[$i]['label'] = str_replace("[$k]", $v, $options[$i]['label']);
                }
            } else {
                $options[$i]['label'] = $d->{$field_label};
            }
            $options[$i]['value'] = $d->{$field_value};
        }
    endif;
} //end if not parent select
?>
<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>

            @foreach ($options as $i => $option)
                <option {!! $option['select'] !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
            @endforeach

        </select>
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>