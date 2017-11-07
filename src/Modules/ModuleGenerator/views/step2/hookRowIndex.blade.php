<div class="box-header with-border">
    <h3 class="box-title">Hook Row Index
        <small>hookRowIndex($columnIndex,&$columnValue)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! CB::icon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
    <div class="alert alert-info"><i class="fa fa-info"></i> You can override the column value.
        <code>$columnIndex</code>
        is for column number start from 0. <code>$columnValue</code> is value that you can override
    </div>
    <textarea id='textarea-hookrowindex' class="form-control" name="hookRowIndex">{{$hookRowIndex}}</textarea>
</div>
        