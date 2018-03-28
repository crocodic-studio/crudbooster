<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        $options = [];
        if (@$formInput['options']['enum']):
            $selects_value = $formInput['options']['value'];
            foreach ($formInput['options']['enum'] as $i => $d) {
                $options[$i]['label'] = $d;
                if ($selects_value) {
                     $options[$i]['value'] = $selects_value[$i];
                } else {
                     $options[$i]['value'] = $d;
                }
            }
        endif;
        ?>
        @foreach ($options as $i => $option)
            <div data-val='{!! $val !!}' class='checkbox {{$disabled}}'>
                <label>
                    <input type='checkbox'
                           {{$disabled}}
                           {!! is_checked($formInput['options']['result_format'], $value, $option['value']) !!}
                            name='{!! $name !!}[]'
                            value='{!! $option['value'] !!}'>
                    {!! $option['label'] !!}
                </label>
            </div>
        @endforeach

        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])

    </div>
</div>