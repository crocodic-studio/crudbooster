
<table id='table-module' class="table table-hover table-striped table-bordered">
    <thead>
        <tr class="active">
            @foreach(module()->getColumnSingleton()->getIndexColumns() as $column)
                <td>
                    {{ $column->getLabel() }}
                </td>
            @endforeach
        </tr>
    </thead>
    <tbody>

    @if(!empty($result) && count($result)==0)
        <tr class='warning'>
            <td colspan="{{ count(module()->getColumnSingleton()->getIndexColumns()) }}">
                <i class='fa fa-table'></i> {{trans("crudbooster.table_data_not_found")}}
            </td>
        </tr>
    @endif

    @foreach($result as $row)
        <tr>
            @foreach(module()->getColumnSingleton()->getIndexColumns() as $column)
                <td>
                    {!! getTypeHook($column->getType())->indexRender($row, $column) !!}
                </td>
            @endforeach
        </tr>
    @endforeach

    </tbody>
</table>

<div class="col-md-8">{!! $result->appends(request()->all())->links() !!}</div>

<?php
$from = $result->count() ? ($result->perPage() * $result->currentPage() - $result->perPage() + 1) : 0;
$to = $result->perPage() * $result->currentPage() - $result->perPage() + $result->count();
$total = $result->total();
?>
<div class="col-md-4" style="margin:30px 0;">
    <span class="pull-right">{{ trans("crudbooster.filter_rows_total") }}
        : {{ $from }} {{ trans("crudbooster.filter_rows_to") }} {{ $to }} {{ trans("crudbooster.filter_rows_of") }} {{ $total }}</span>
</div>
