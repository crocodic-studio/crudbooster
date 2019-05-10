
<table id='table-module' class="table table-hover table-striped table-bordered">
    <thead>
        <tr>
            @foreach(module()->getColumnSingleton()->getIndexColumns() as $column)
                @php /** @var $column \crocodicstudio\crudbooster\models\ColumnModel */ @endphp
                <th width="{{ $column->getColumnWidth()?:"auto" }}">
                    <a title="Click to sorting by {{ $column->getLabel() }}" href="{{ request()->fullUrlWithQuery(['order_by'=>$column->getField(),'order_sort'=>((request('order_sort')=='desc')?'asc':'desc') ]) }}">{{ $column->getLabel() }}
                        @if(request('order_by')==$column->getField())
                            <i class="fa fa-arrow-circle-{{ (request('order_sort')=='asc')?'down':'up' }}"></i>
                        @endif
                    </a>
                </th>
            @endforeach
            <th width="150px" style="text-align: center">
                Action
            </th>
        </tr>
    </thead>
    <tbody>

    @if(!empty($result) && count($result)==0)
        <tr class='warning'>
            <td colspan="{{ count(module()->getColumnSingleton()->getIndexColumns())+1 }}" style="text-align: center">
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
            <td style="text-align: center; white-space: nowrap">
                @include("crudbooster::module.index.table_action_buttons")
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
