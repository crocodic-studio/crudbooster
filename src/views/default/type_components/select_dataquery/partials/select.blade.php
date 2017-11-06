<?php
$options = [];
if (! $form['options']['parent_select'] && @$form['options']['query']):
    foreach (DB::select(DB::raw($form['options']['query'])) as $index => $d) {
        $options[$i]['select'] = ($value == $d->{$form['options']['field_value']}) ? "selected" : "";
        if ($form['options']['format']) {
            $options[$i]['label'] = $form['options']['format'];
            foreach ($d as $k => $v) {
                $options[$i]['label'] = str_replace("[".$k."]", $v, $options[$i]['label']);
            }
        } else {
            $options[$i]['label'] = $d->{$form['options']['field_label']};
        }
        $options[$i]['value'] = $d->{$form['options']['field_value']};
    }
endif;
?>


<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>

            @foreach ($options as $option)
                <option {!! $option['select'] !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
            @endforeach
        </select>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>