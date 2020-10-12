<div class='bootstrap-timepicker'>
    <div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
        <label class='control-label col-sm-2'>{{$form['label']}}
            @if($required)
                <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
            @endif
        </label>

        <div class="{{$col_width?:'col-sm-10'}}">
            <div class="input-group">
                @if(!$disabled)
                    <span class="input-group-addon"><i class='fa fa-clock-o'></i></span>
                @endif
                <input type='text' title="{{$form['label']}}"
                       {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus timepicker' name="{{$name}}" id="{{$name}}"
                       readonly value='{{$value}}'/>
            </div>
            <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
            <p class='help-block'>{{ @$form['help'] }}</p>
        </div>
    </div>
</div>