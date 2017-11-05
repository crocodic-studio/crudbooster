<?php $name_column = $name.$col['name']; ?>

<div id='{{$name_column}}' class="input-group">
    <input type="hidden" class="input-id">
    <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
    <span class="input-group-btn">
        <button class="btn btn-primary" onclick="showModal{{$name_column}}()" type="button">
            <i class='fa fa-search'></i> {{cbTrans('datamodal_browse_data')}}
        </button>
  </span>
</div><!-- /input-group -->

@include('crudbooster::default.type_components.modal_dialog', ['name' => $name_column, 'label'=> $col['label'], 'size' => $col['datamodal_size']])

@push('bottom')
    <script type="text/javascript">
        var url_{{$name_column}} = "{{CRUDBooster::mainpath('modal-data')}}?table={{$col['datamodal_table']}}&columns=id,{{$col['datamodal_columns']}}&name_column={{$name_column}}&where={{urlencode($col['datamodal_where'])}}&select_to={{ urlencode($col['datamodal_select_to']) }}&columns_name_alias={{urlencode($col['datamodal_columns_alias'])}}";
        var url_is_setted_{{$name_column}} = false;

        function showModal {{$name_column}}() {
            if (url_is_setted_{{$name_column}} == false) {
                url_is_setted_{{$name_column}} = true;
                $('#iframe-modal-{{$name_column}}').attr('src', url_{{$name_column}});
            }
            $('#modal-datamodal-{{$name_column}}').modal('show');
        }

        function hideModal {{$name_column}}() {
            $('#modal-datamodal-{{$name_column}}').modal('hide');
        }

        function selectAdditionalData {{$name_column}}(select_to_json) {
            $.each(select_to_json, function (key, val) {
                console.log('#' + key + ' = ' + val);
                if (key == 'datamodal_id') {
                    $('#{{$name_column}} .input-id').val(val);
                }
                if (key == 'datamodal_label') {
                    $('#{{$name_column}} .input-label').val(val);
                }
                $('#{{$name}}' + key).val(val).trigger('change');
            })
            hideModal{{$name_column}}();
        }
    </script>
@endpush