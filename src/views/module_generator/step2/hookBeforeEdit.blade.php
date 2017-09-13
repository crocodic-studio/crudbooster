<div class="box-header with-border">
    <h3 class="box-title">Hook Before Edit
        <small>hookBeforeEdit(&$postdata,$id)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! CB::icon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
    <div class="alert alert-info"><i class="fa fa-info"></i> You can override the post data before edit
    </div>
    <textarea id='textarea-hookBeforeEdit' class="form-control"
              name="hookBeforeEdit">{{$hookBeforeEdit}}</textarea>
</div>
        