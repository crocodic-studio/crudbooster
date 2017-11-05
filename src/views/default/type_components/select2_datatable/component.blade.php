@push('bottom')
    <script>
        $(function () {
            $('#{{$name}}').select2({
                @if($form['options']['multiple']==true)
                multiple: true,
                @endif
                placeholder: "{{ ($form['placeholder'])?:cbTrans('text_prefix_option')." ".$form['label'] }}",
                allowClear: {{$form['options']['allow_clear']?'true':'false'}},
                escapeMarkup: function (markup) {
                    return markup;
                },

                @if($form['options']['ajax_mode']==true)
                minimumInputLength: 1,
                ajax: {
                    type: 'POST',
                    url: '{{ CRUDBooster::mainpath("find-data") }}',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            q: params.term,
                            _token: '{{csrf_token()}}',
                            data: "<?php echo base64_encode(json_encode($form['options'])) ?>",
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: data.items
                        };
                    }
                }

                @endif
            });

        })
    </script>
@endpush


<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select style='width:100%' class='form-control' id="{{$name}}"
                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($form['options']['multiple']==true)?'[]':''}}" {{ ($form['options']['multiple'])?'multiple="multiple"':'' }} >

            @php
                $select_table = $form['options']['table'];
                $select_label = $form['options']['field_label'];
                $select_value = $form['options']['field_value'];
                $select_where = $form['options']['sql_where'];
            @endphp

            @if($form['options']['ajax_mode'] == false)
                @if($form['options']['multiple']==false)
                    <option value=''>{{cbTrans('text_prefix_option')}} {{$form['label']}}</option>
                @endif
                <?php

                $result = DB::table($select_table)->select($select_value, $select_label);
                $result->addSelect(DB::raw("CONCAT(".$select_label.") as select2_text"));
                if ($select_where) {
                    $result->whereraw($select_where);
                }
                if ($form['options']['sql_orderby']) {
                    $result->orderByRaw($form['options']['sql_orderby']);
                } else {
                    $result->orderBy('select2_text', 'asc');
                }

                if (Schema::hasColumn($form['options']['table'], 'deleted_at')) {
                    $result->whereNull('deleted_at');
                }

                $result = $result->get();

                foreach ($result as $row) {
                    $option_label = $row->select2_text;
                    $option_value = $row->{$form['options']['field_value']};

                    if ($form['options']['format']) {
                        $option_label = $form['options']['format'];
                        foreach ($row as $k => $v) {
                            $option_label = str_replace("[".$k."]", $v, $option_label);
                        }
                    }
                    echo "<option ".findSelected($value, $form, $option_value)." value='$option_value'>$option_label</option>";
                }
                ?>
            <!--end-datatable-ajax-->
            @else
                @if($value)
                    @php
                        $rawvalue = $value;
                        $data = DB::table($select_table);
                        $result = $data->get();
                    @endphp
                    @foreach($result as $r)
                        @php
                            $option_value = $r->$select_value;

                            if($form['options']['format']) {
                                $option_label = $form['options']['format'];
                                foreach($r as $k=>$v) {
                                    $option_label = str_replace("[".$k."]", $v, $option_label);
                                }
                            }else{
                                $option_label = $r->$select_label;
                            }
                        @endphp
                        <option value="{{$option_value}}" {{findSelected($value, $form, $option_value)}} >{{$option_label}}</option>
                    @endforeach
                @endif
            @endif
        </select>
        <div class="text-danger">
            {!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
        </div><!--end-text-danger-->
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
</div>