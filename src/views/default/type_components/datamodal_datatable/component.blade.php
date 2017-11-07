<?php
$dm_label = $formInput['options']['column_label'];
$dm_value = $formInput['options']['column_value'];
$dm_table = $formInput['options']['table'];
$datamodal_value = DB::table($dm_table)->where($dm_value, $value)->first()->$dm_label;
?>
<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'
     id='form-group-{{$name}}' style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">


        <div id='{{$name}}' class="input-group">
            <input type="hidden" name="{{$name}}" id='datamodal-input-value-{{$name}}' value="{{$value}}">
            <input type="text" id='datamodal-input-label-{{$name}}'
                   class="form-control input-label {{$required?"required":""}}"
                   {{$required?"required":""}} value="{{$datamodal_value}}" readonly>
            <span class="input-group-btn">
        <button class="btn btn-primary" onclick="showModal{{$name}}()" type="button"><i
                    class='fa fa-search'></i> {{cbTrans('datamodal_browse_data')}}</button>
      </span>
        </div><!-- /input-group -->

        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>

@push('bottom')
    <script type="text/javascript">
        var url_{{$name}} = "{{CRUDBooster::mainpath('data-modal-datatable')}}?" +
            "data={!!base64_encode(json_encode($formInput['options']))!!}" +
            "&name={{$name}}";

        function showModal {{$name}}() {
            $('#iframe-modal-{{$name}}').attr('src', url_{{$name}});
            $('#modal-datamodal-{{$name}}').modal('show');
        }

        function hideModal {{$name}}() {
            $('#modal-datamodal-{{$name}}').modal('hide');
        }

        function selectDataModal {{$name}}(label, value) {
            $('#datamodal-input-label-{{$name}}').val(label);
            $('#datamodal-input-value-{{$name}}').val(value);
            @if($formInput['options']['onselect_callback'])
            {!! $formInput['options']['onselect_callback'] !!}
            @endif
            hideModal{{$name}}();
        }
    </script>


@include('crudbooster::default.type_components.modal_dialog', ['name' => $name, 'label'=> $label, 'size' => $formInput['options']['size']])
@endpush
