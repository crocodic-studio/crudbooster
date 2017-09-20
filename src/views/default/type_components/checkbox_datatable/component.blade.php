<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        if (@$form['options']['table']):
            $field_label = $form['options']['field_label'];
            $field_value = $form['options']['field_value'];

            $selects_data = DB::table($form['options']['table']);

            if (\Schema::hasColumn($form['options']['table'], 'deleted_at')) {
                $selects_data->where('deleted_at', NULL);
            }

            if (@$form['options']['sql_where']) {
                $selects_data->whereRaw($form['options']['sql_where']);
            }

            $selects_data->addselect($field_label);
            $selects_data->addselect($field_value);
            if ($form['options']['sql_orderby']) {
                $selects_data->orderByRaw($form['options']['sql_orderby']);
            } else {
                $selects_data->orderby($field_value, 'desc');
            }
            $selects_data = $selects_data->get();

            foreach ($selects_data as $d) {

                switch ($form['options']['result_format']) {
                    case 'JSON':
                        $valueFormat = json_decode($value, true);
                        $checked = (in_array($d->$field_value, $valueFormat)) ? "checked" : "";
                        break;
                    default:
                    case 'COMMA_SEPARATOR':
                        $valueFormat = explode(', ', $value);
                        $checked = (in_array($d->$field_value, $valueFormat)) ? "checked" : "";
                        break;
                    case 'SEMICOLON_SEPARATOR':
                        $valueFormat = explode('; ', $value);
                        $checked = (in_array($d->$field_value, $valueFormat)) ? "checked" : "";
                        break;
                }

                echo "
        <div data-val='$val' class='checkbox $disabled'>
            <label>
                <input type='checkbox' $disabled $checked name='".$name."[]' value='".$d->$field_value."'>
                ".$d->$field_label."
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