<?php
$options = [];
if (! $formInput['options']['parent_select'] && @$formInput['options']['query']):
    foreach (DB::select(DB::raw($formInput['options']['query'])) as $index => $d) {
        $options[$i]['select'] = ($value == $d->{$formInput['options']['field_value']}) ? "selected" : "";
        if ($formInput['options']['format']) {
            $options[$i]['label'] = $formInput['options']['format'];
            foreach ($d as $k => $v) {
                $options[$i]['label'] = str_replace("[".$k."]", $v, $options[$i]['label']);
            }
        } else {
            $options[$i]['label'] = $d->{$formInput['options']['field_label']};
        }
        $options[$i]['value'] = $d->{$formInput['options']['field_value']};
    }
endif;
?>


<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>

            @foreach ($options as $option)
                <option {!! $option['select'] !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
            @endforeach
        </select>
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>