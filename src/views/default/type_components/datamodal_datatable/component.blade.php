<div class='form-group form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'
     id='form-group-{{$name}}' style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        $dm_label = $form['options']['column_label'];
        $dm_value = $form['options']['column_value'];
        $dm_table = $form['options']['table'];
        $datamodal_value = DB::table($dm_table)->where($dm_value, $value)->first()->$dm_label;
        ?>

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

        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>

@push('bottom')
<script type="text/javascript">
    var url_{{$name}} = "{{CRUDBooster::mainpath('data-modal-datatable')}}?" +
        "data={!!base64_encode(json_encode($form['options']))!!}" +
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
        @if($form['options']['onselect_callback'])
            {!! $form['options']['onselect_callback'] !!}
        @endif
        hideModal{{$name}}();
    }
</script>


<div id='modal-datamodal-{{$name}}' class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog {{ $form['options']['size']=='large'?'modal-lg':'' }} " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class='fa fa-search'></i> {{cbTrans('datamodal_browse_data')}}
                    | {{$form['label']}}</h4>
            </div>
            <div class="modal-body">
                <iframe id='iframe-modal-{{$name}}' style="border:0;height: 430px;width: 100%" src=""></iframe>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endpush
