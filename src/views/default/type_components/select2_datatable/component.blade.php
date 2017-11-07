@push('bottom')
    <script>
        $(function () {
            $('#{{$name}}').select2({
                @if($formInput['options']['multiple']==true)
                multiple: true,
                @endif
                placeholder: "{{ ($formInput['placeholder'])?:cbTrans('text_prefix_option')." ".$label }}",
                allowClear: {{$formInput['options']['allow_clear']?'true':'false'}},
                escapeMarkup: function (markup) {
                    return markup;
                },

                @if($formInput['options']['ajax_mode']==true)
                minimumInputLength: 1,
                ajax: {
                    type: 'POST',
                    url: '{{ CRUDBooster::mainpath("find-data") }}',
                    delay: 250,
                    data: function (params) {
                        var query = {
                            q: params.term,
                            _token: '{{csrf_token()}}',
                            data: "<?php echo base64_encode(json_encode($formInput['options'])) ?>",
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
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select style='width:100%' class='form-control' id="{{$name}}"
                {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($formInput['options']['multiple']==true)?'[]':''}}" {{ ($formInput['options']['multiple'])?'multiple="multiple"':'' }} >

            @php
                $select_table = $formInput['options']['table'];
                $select_label = $formInput['options']['field_label'];
                $select_value = $formInput['options']['field_value'];
                $select_where = $formInput['options']['sql_where'];
            @endphp

            @if($formInput['options']['ajax_mode'] == false)
                @include('crudbooster::default.type_components.select2_datatable.partials.severside_options')
            @else

            @endif
        </select>
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])

    </div>
</div>