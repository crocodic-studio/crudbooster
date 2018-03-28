<div id='modal-datamodal-{{$name}}' class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog {{ $size == 'large'?'modal-lg':'' }} " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class='fa fa-search'></i> {{cbTrans('datamodal_browse_data')}} {{$label}}
                </h4>
            </div>
            <div class="modal-body">
                <iframe id='iframe-modal-{{$name}}' style="border:0;height: 430px;width: 100%" src=""></iframe>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
