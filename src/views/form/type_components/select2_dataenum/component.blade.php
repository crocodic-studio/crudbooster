@push('bottom')
    <script>
        $(function () {
            $('#{{$name}}').select2({
                @if($formInput['options']['multiple']==true)
                multiple: true,
                @endif
                placeholder: "{{ ($formInput['placeholder'])?:cbTrans('text_prefix_option')." ".$label }}",
                allowClear: {{$formInput['options']['allow_clear']?'true':'false'}}
            });
        })
    </script>
@endpush


<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='".cbTrans('this_field_is_required')."'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select style='width:100%' class='form-control' id="{{$name}}"
                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($formInput['options']['multiple']==true)?'[]':''}}" {{ ($formInput['options']['multiple'])?'multiple="multiple"':'' }} >

            @if($formInput['options']['multiple']==false)
                <option value=''>{{cbTrans('text_prefix_option')}} {{$label}}</option>
            @endif

            @php
                @$enum = $formInput['options']['enum'] ?: [];
                @$enumValue = $formInput['options']['value'];
            @endphp
            @foreach($enum as $i=>$e)
                @if($enumValue)
                    @php
                        $selected = ($enumValue[$i]==$value)?"selected":"";
                    @endphp
                    <option value="{{$enumValue[$i]}}" {{$selected}} >{{$e}}</option>
                @else
                    @php
                        $selected = ($e==$value)?"selected":"";
                    @endphp
                    <option value="{{$e}}" {{$selected}} >{{$e}}</option>
                @endif
            @endforeach
        </select>
        {!! underField($formInput['help'], $errors->first($name)) !!}

    </div>
</div>