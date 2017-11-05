<?php
if (! $form['options']['parent_select']) {

    if (@$form['options']['table']):

        $table = $form['options']['table'];
        $field_label = $form['options']['field_label'];
        $field_value = $form['options']['field_value'];

        $selects_data = DB::table($table);

        if (\Schema::hasColumn($table, 'deleted_at')) {
            $selects_data->where($table.'.deleted_at', NULL);
        }

        if (@$form['options']['sql_where']) {
            $selects_data->whereraw($form['options']['sql_where']);
        }

        if ($form['options']['sql_orderby']) {
            $selects_data = $selects_data->orderByRaw($form['options']['sql_orderby'])->get();
        } else {
            $selects_data = $selects_data->orderby($field_value, "desc")->get();
        }

        $options = [];
        foreach ($selects_data as $i => $d) {
            $options[$i]['select'] = ($value == $d->{$field_value}) ? "selected" : "";

            if ($form['options']['format']) {
                $options[$i]['label'] = $form['options']['format'];
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
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>

            @foreach ($options as $i => $option)
                <option {!! $option['select'] !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
            @endforeach

        </select>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>