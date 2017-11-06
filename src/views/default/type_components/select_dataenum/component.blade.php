<?php $default = ! empty($formInput['placeholder']) ? $formInput['placeholder'] : cbTrans('text_prefix_option')." ".$label;?>

<?php
$options = [];
$enumValue = $formInput['options']['value'];
foreach ($formInput['options']['enum'] as $i => $e) {
    $options[$i]['label'] = $e;
    $options[$i]['value'] = ($enumValue) ? $enumValue[$i] : $e;
    $options[$i]['select'] = ($value && $value == $options[$i]['value']) ? "selected" : "";
}
?>

<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>
            @foreach($options as $option)
                <option {!! $option['select'] !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
            @endforeach
        </select>
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>
