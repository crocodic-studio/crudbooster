<div class="box-header with-border">
    <h3 class="box-title">Hook Query Index
        <small>hook_query_index(&$query)</small>
    </h3>
    <div class="box-tools">
        <button class="btn btn-success btn-sm" type="submit">
            {!! cbIcon('save') !!}Save Module
        </button>
    </div>
</div>
<div class="box-body">
    <div class="alert alert-info"><i class="fa fa-info"></i> You can use $query to extend Laravel Database
        <a href='https://laravel.com/docs/queries' target='_blank'>Query Builder</a>. E.g: <code>$query->where('status','active');</code>
    </div>
    <textarea id='textarea-hookqueryindex' class="form-control" name="hook_query_index">{{$hookQueryIndex}}</textarea>
</div>
        