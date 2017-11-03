<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        if (@$form['options']['enum']):
            $selects_data = $form['options']['enum'];
            $selects_value = $form['options']['value'];

            foreach ($selects_data as $i => $d) {
                $option_label = $d;
                if ($selects_value) {
                    $option_value = $selects_value[$i];
                } else {
                    $option_value = $d;
                }
              ?>
            <div data-val='{!! $val !!}' class='checkbox {{$disabled}}'>
                <label>
                    <input type='checkbox'  {{$disabled}} {!! is_checked($form['options']['result_format'], $value, $option_value) !!}
                     name='{!! $name !!}[]'
                     value='{!! $option_value !!}'>
                     {!! $option_label !!}
                </label>
            </div>
        <?php } endif; ?>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>