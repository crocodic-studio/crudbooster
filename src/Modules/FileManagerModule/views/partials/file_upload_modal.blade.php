<div id='modal-upload-file' class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Upload a file</h4>
            </div>


            <form enctype="multipart/form-data" method="post"
                  action="{{route('AdminFileManagerControllerPostUpload')}}">
                <input type="hidden" name='_token' value="{{csrf_token()}}">
                <input type="hidden" name="path" value="{{g('path')}}">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Filename</label>
                        <input type="file" name="userfile" class="form-control" required>
                        <div class="help-block">
                            File type support only {{ cbConfig('UPLOAD_TYPES') }}
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>


            </form>


        </div>
    </div>
</div>