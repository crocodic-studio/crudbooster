@push('bottom')
    <script>
        $(function () {
            $('#{{$name}}').select2({
                @if($form['options']['multiple']==true)
                multiple: true,
                @endif
                placeholder: "{{ ($form['placeholder'])?:cbTrans('text_prefix_option')." ".$label }}",
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
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

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
                @include('crudbooster::default.type_components.select2_datatable.partials.severside_options')
            @else

            @endif
        </select>
        <div class="text-danger">
            {!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
        </div><!--end-text-danger-->
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
</div>