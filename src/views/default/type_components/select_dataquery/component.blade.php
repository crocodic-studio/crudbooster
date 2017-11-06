<?php
$default = ! empty($formInput['placeholder']) ? $formInput['placeholder'] : cbTrans('text_prefix_option')." ".$label;
$query = str_random(5);
\Cache::forget($query);
\Cache::forever($query, $formInput['options']['query']);
?>
@if($formInput['options']['parent_select'])
    <script type="text/javascript">
        $(function () {
            $('#{{$formInput['options']['parent_select']}}').change(function () {
                var $current = $("#{{$formInput['name']}}");
                var parent_id = $(this).val();
                var fk_name = "{{$formInput['options']['parent_select']}}";
                var fk_value = $(this).val();
                var value = "{{$value}}";

                if (fk_value != '') {
                    $current.html("<option value=''>{{cbTrans('text_loading')}} {{$label}}</option>");
                    $.get("{{CRUDBooster::mainpath('data-query')}}?query=" + query + "&fk_name=" + fk_name + "&fk_value=" + fk_value, function (response) {
                        if (response.items.length > 0) {
                            $current.html("<option value=''>{{$default}}</option>");
                            $.each(response.items, function (i, obj) {
                                var selected = (value && value == obj.{{$formInput['options']['field_value']}}) ? "selected" : "";
                                var label = obj.{{$formInput['options']['field_label']}};
                                var format = "{{$formInput['options']['format']}}";
                                if (format != '') {
                                    $.each(obj, function (e, o) {
                                        format.replace("[" + e + "]", o);
                                    })
                                    label = format;
                                }
                                $("<option " + selected + " value='" + obj.{{$formInput['options']['field_value']}}+ "'>" + label + "</option>").appendTo("#{{$formInput['name']}}");
                            })
                            $current.trigger('change');
                        }
                    });
                } else {
                    $current.html("<option value=''>{{$default}}</option>");
                }
            })

            $('#{{$formInput['options']['parent_select']}}').trigger('change');
            $("#{{$formInput['name']}}").trigger('change');
        })
    </script>
@endif

@include('crudbooster::default.type_components.select_dataquery.partials.select')
