<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        $options = [];
        if (@$form['options']['enum']):
            $selects_value = $form['options']['value'];
            foreach ($form['options']['enum'] as $i => $d) {
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
                           {!! is_checked($form['options']['result_format'], $value, $option['value']) !!}
                            name='{!! $name !!}[]'
                            value='{!! $option['value'] !!}'>
                    {!! $option['label'] !!}
                </label>
            </div>
        @endforeach
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>