<div class="box-header with-border">
    <h3 class="box-title">Hook After Edit
        <small>hookAfterEdit($id)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! CB::icon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
    <textarea id='textarea-hookAfterEdit' class="form-control" name="hookAfterEdit">{{$hookAfterEdit}}</textarea>
</div>
        