<?php $default = ! empty($formInput['placeholder']) ? $formInput['placeholder'] : cbTrans('text_prefix_option')." ".$label;?>
@if($formInput['options']['parent_select'])
    <script type="text/javascript">
        $(function () {
            $('#{{$formInput['options']['parent_select']}}').change(function () {
                var $current = $("#{{$formInput['name']}}");
                var parent_id = $(this).val();
                var fk_name = "{{$formInput['options']['parent_select']}}";
                var fk_value = $(this).val();
                var table = "{{$formInput['options']['table']}}";
                var label = "{{$formInput['options']['field_label']}}";
                var value = "{{$value}}";

                if (fk_value != '') {
                    $current.html("<option value=''>{{cbTrans('text_loading')}} {{$label}}");
                    $.get("{{CRUDBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value, function (response) {
                        if (response) {
                            $current.html("<option value=''>{{$default}}");
                            $.each(response, function (i, obj) {
                                var selected = (value && value == obj.select_value) ? "selected" : "";
                                $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$formInput['name']}}");
                            })
                            $current.trigger('change');
                        }
                    });
                } else {
                    $current.html("<option value=''>{{$default}}");
                }
            })

            $('#{{$formInput['options']['parent_select']}}').trigger('change');
            $("#{{$formInput['name']}}").trigger('change');
        })
    </script>
@endif
@include('crudbooster::default.type_components.select_datatable.partials.select_options')