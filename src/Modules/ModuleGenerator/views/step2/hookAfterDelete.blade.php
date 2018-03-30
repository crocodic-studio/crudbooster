<div class="box-header with-border">
    <h3 class="box-title">Hook After Delete
        <small>hook_after_delete($id)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! cbIcon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
    <textarea id='textarea-hookAfterDelete' class="form-control" name="hook_after_delete">{{$hookAfterDelete}}</textarea>
</div>
        