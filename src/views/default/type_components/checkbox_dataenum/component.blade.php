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

                switch ($form['options']['result_format']) {
                    case 'JSON':
                        $valueFormat = json_decode($value, true);
                        $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                        break;
                    default:
                    case 'COMMA_SEPARATOR':
                        $valueFormat = explode(', ', $value);
                        $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                        break;
                    case 'SEMICOLON_SEPARATOR':
                        $valueFormat = explode('; ', $value);
                        $checked = (in_array($option_value, $valueFormat)) ? "checked" : "";
                        break;
                }

                echo "
        <div data-val='$val' class='checkbox $disabled'>
            <label>
                <input type='checkbox' $disabled $checked name='".$name."[]' value='".$option_value."'>
                ".$option_label."
            </label>
        </div>
        ";
            }

        endif;
        ?>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>