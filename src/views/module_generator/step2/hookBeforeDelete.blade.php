<div class="box-header with-border">
    <h3 class="box-title">Hook Before Delete
        <small>hookBeforeDelete($id)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! CB::icon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
                <textarea id='textarea-hookBeforeDelete' class="form-control"
                          name="hookBeforeDelete">{{$hookBeforeDelete}}</textarea>
</div>
        