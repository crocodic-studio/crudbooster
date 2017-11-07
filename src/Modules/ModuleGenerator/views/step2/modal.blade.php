<div id="modal-callback" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Callback</h4>
            </div>
            <div class="modal-body">
                <textarea id="textareaCallback" class="form-control"></textarea>
                <p>
                <div class="help-block">Must return a value <code>E.g: return number_format($row->price);</code>
                </div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="saveModalCallback()" class="btn btn-primary btn-save">Save
                    changes
                </button>
            </div>
        </div>
    </div>
</div>