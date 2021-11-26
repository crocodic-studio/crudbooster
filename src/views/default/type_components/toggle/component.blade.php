<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>
        {{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! cbLang('this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <input type='checkbox' data-toggle="toggle" data-size="small" title="{{$form['label']}}"
               {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} {{$validation['max']?"maxlength=".$validation['max']:""}} class='form-control checkboxtoggle'
               name="{{$name}}" id="{{$name}}" value="{{$value ? "1" : "0"}}" @if($value) checked
        @else @endif/>

        <div
            class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
</div>

@push('bottom')
    <script type="text/javascript">
        if ($.fn.bootstrapToggle) {
            $('#{{$name}}').bootstrapToggle({
                on: "{{ cbLang("confirmation_yes") }}",
                off: "{{ cbLang("confirmation_no") }}"
            });
        }
        $(document).on('change', '#{{$name}}', function () {
            let input = $(this);
            input.val(input.prop('checked') ? 1 : 0);
        });
    </script>
@endpush
