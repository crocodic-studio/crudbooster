<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'
     id='form-group-{{$name}}' style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <div class="input-group">
            <span class="input-group-addon open-datetimepicker"><a><i class='fa fa-calendar '></i></a></span>
            <input type='text' title="{{$label}}" readonly
                   {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus input_date'
                   name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
        </div>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$formInput['help'] }}</p>
    </div>
</div>
