<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'
     id='form-group-{{$name}}' style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <div class="input-group">

            <span class="input-group-addon"><a href='javascript:void(0)'
                                               onclick='$("#{{$name}}").data("daterangepicker").toggle()'><i
                            class='fa fa-calendar'></i></a></span>

            <input type='text' title="{{$label}}" readonly
                   {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus datetimepicker'
                   name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
        </div>

        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>