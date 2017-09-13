<?php $default = !empty($form['placeholder']) ? $form['placeholder'] : cbTrans('text_prefix_option') . " " . $form['label'];?>
@if($form['options']['parent_select'])
    <script type="text/javascript">
        $(function () {
            $('#{{$form['options']['parent_select']}}').change(function () {
                var $current = $("#{{$form['name']}}");
                var parent_id = $(this).val();
                var fk_name = "{{$form['options']['parent_select']}}";
                var fk_value = $(this).val();
                var table = "{{$form['options']['table']}}";
                var label = "{{$form['options']['field_label']}}";
                var value = "{{$value}}";

                if (fk_value != '') {
                    $current.html("<option value=''>{{cbTrans('text_loading')}} {{$form['label']}}");
                    $.get("{{CRUDBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value, function (response) {
                        if (response) {
                            $current.html("<option value=''>{{$default}}");
                            $.each(response, function (i, obj) {
                                var selected = (value && value == obj.select_value) ? "selected" : "";
                                $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$form['name']}}");
                            })
                            $current.trigger('change');
                        }
                    });
                } else {
                    $current.html("<option value=''>{{$default}}");
                }
            })

            $('#{{$form['options']['parent_select']}}').trigger('change');
            $("#{{$form['name']}}").trigger('change');
        })
    </script>
@endif
<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>
            <?php
            if (!$form['options']['parent_select']) {

                if (@$form['options']['table']):

            $table = $form['options']['table'];
            $field_label = $form['options']['field_label'];
            $field_value = $form['options']['field_value'];
            $selects_data = DB::table($table);

            if(\Schema::hasColumn($table,'deleted_at')) {
            $selects_data->where($table.'.deleted_at',NULL);
            }

            if(@$form['options']['sql_where']) {
            $selects_data->whereraw($form['options']['sql_where']);
            }

            if($form['options']['sql_orderby']) {
            $selects_data = $selects_data->orderByRaw($form['options']['sql_orderby'])->get();
            }else{
            $selects_data = $selects_data->orderby($form['options']['field_value'],"desc")->get();
            }


            foreach($selects_data as $d) {
            $select = ($value == $d->{$form['options']['field_value']})?"selected":"";
            if($form['options']['format']) {
            $label = $form['options']['format'];
            foreach($d as $k=>$v) {
            $label = str_replace("[".$k."]", $v, $label);
            }
            }else{
            $label = $d->{$form['options']['field_label']};
            }
            echo "
            <option $select value='".$d->{$form[' options
            ']['field_value']}."'>".$label."</option>";
            }
            endif;
            } //end if not parent select
            ?>
        </select>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>
