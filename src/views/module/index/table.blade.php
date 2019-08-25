<table id='table-module' class="table table-hover table-striped table-bordered">
    <thead>
        <tr>
            @if($position = getSetting("table_module_action_button_position"))
                @if($position == "left")
                    <th width="150px" style="text-align: center">
                        {{ cbLang('action') }}
                    </th>
                @endif
            @endif

            @foreach(module()->getColumnSingleton()->getIndexColumns() as $column)
                @php /** @var $column \crocodicstudio\crudbooster\models\ColumnModel */ @endphp
                <th width="{{ $column->getColumnWidth()?:"auto" }}">
                    <a title="Click to sorting {{ (request('order_sort')=='asc')?'asc':'desc' }} by {{ $column->getLabel() }}" href="{{ request()->fullUrlWithQuery(['order_by'=>$column->getOrderByColumn(),'order_sort'=>((request('order_sort')=='desc')?'asc':'desc') ]) }}">{{ $column->getLabel() }}
                        @if(request('order_by')==$column->getOrderByColumn())
                            <i class="pull-right fa fa-arrow-circle-{{ (request('order_sort')=='asc')?'down':'up' }}"></i>
                        @endif
                    </a>
                </th>
            @endforeach

            @if($position = getSetting("table_module_action_button_position","right"))
                @if($position == "right")
                    <th width="150px" style="text-align: center">
                        {{ cbLang('action') }}
                    </th>
                @endif
            @endif

        </tr>
    </thead>
    <tbody>

    @if(!empty($result) && count($result)==0)
        <tr class='warning'>
            <td colspan="{{ count(module()->getColumnSingleton()->getIndexColumns())+1 }}" style="text-align: center">
                <i class='fa fa-table'></i> {{cbLang("table_data_not_found")}}
            </td>
        </tr>
    @endif

    @foreach($result as $row)
        <tr>
            @if($position = getSetting("table_module_action_button_position"))
                @if($position == "left")
                    <td style="text-align: center; white-space: nowrap">
                        @include("crudbooster::module.index.table_action_buttons")
                    </td>
                @endif
            @endif

            @foreach(module()->getColumnSingleton()->getIndexColumns() as $column)
                <td>
                    <?php
                        /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */
                        $value = getTypeHook($column->getType())->indexRender($row, $column);
                        $value = call_user_func($column->getIndexDisplayTransform(), $value, $row);
                        echo $value;
                    ?>
                </td>
            @endforeach
            @if($position = getSetting("table_module_action_button_position","right"))
                @if($position == "right")
                    <td style="text-align: center; white-space: nowrap">
                        @include("crudbooster::module.index.table_action_buttons")
                    </td>
                @endif
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
