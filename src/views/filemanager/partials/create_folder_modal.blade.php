<div id='modal-create-directory' class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Create a directory</h4>
            </div>

            <form method="post" action="{{route('AdminFileManagerControllerPostCreateDirectory')}}">
                <input type="hidden" name='_token' value="{{csrf_token()}}">
                <input type="hidden" name="path" value="{{g('path')}}">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Directory Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>

            </form>
        </div>
    </div>
</div>