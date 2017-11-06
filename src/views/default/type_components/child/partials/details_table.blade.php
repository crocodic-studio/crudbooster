<?php
$columns_tbody = [];
$data_child = DB::table($formInput['table'])->where($formInput['foreign_key'], $id);
foreach ($formInput['columns'] as $i => $c) {
    $data_child->addselect($formInput['table'].'.'.$c['name']);

    if ($c['type'] == 'datamodal') {
        $datamodal_title = explode(',', $c['datamodal_columns'])[0];
        $datamodal_table = $c['datamodal_table'];
        $data_child->join($c['datamodal_table'], $c['datamodal_table'].'.id', '=', $c['name']);
        $data_child->addselect($c['datamodal_table'].'.'.$datamodal_title.' as '.$datamodal_table.'_'.$datamodal_title);
    } elseif ($c['type'] == 'select') {
        if ($c['datatable']) {
            $join_table = explode(',', $c['datatable'])[0];
            $join_field = explode(',', $c['datatable'])[1];
            $data_child->join($join_table, $join_table.'.id', '=', $c['name']);
            $data_child->addselect($join_table.'.'.$join_field.' as '.$join_table.'_'.$join_field);
        }
    }
}

$data_child = $data_child->orderby($formInput['table'].'.id', 'desc')->get();
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <i class='fa fa-table'></i> {{cbTrans('table_detail')}}
    </div>
    <div class="panel-body no-padding table-responsive" style="max-height: 400px;overflow: auto;">
        <table id='table-{{$name}}' class='table table-striped table-bordered'>
            <thead>
                <tr>
                    @foreach($formInput['columns'] as $col)
                        <th>{{$col['label']}}</th>
                    @endforeach
                    <th width="90px">{{cbTrans('action_label')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data_child as $d)
            <tr>
                @foreach($formInput['columns'] as $col)
                    <td class="{{$col['name']}}">
                        @if ($col['type'] == 'select')
                            @if ($col['datatable'])
                                <?php
                                    $join_table = explode(',', $col['datatable'])[0];
                                    $join_field = explode(',', $col['datatable'])[1];
                                ?>
                                <span class='td-label'>
                                    {!! $d->{$join_table.'_'.$join_field} !!}
                                </span>
                            @endif
                            @if ($col['dataenum'])
                                <span class='td-label'>{!! $d->{$col['name']} !!}</span>
                            @endif

                        @elseif ($col['type'] == 'datamodal')
                            <?php
                            $datamodal_title = explode(',', $col['datamodal_columns'])[0];
                            $datamodal_table = $col['datamodal_table'];
                            ?>
                            <span class='td-label'>
                                {!! $d->{$datamodal_table.'_'.$datamodal_title} !!}
                            </span>

                        @elseif ($col['type'] == 'upload')
                            <?php $filename = basename($d->{$col['name']}); ?>
                            @if ($col['upload_type'] == 'image')
                                <a href='{!! asset($d->{$col['name']}) !!}' data-lightbox='roadtrip'>
                                    <img data-label='{!! $filename !!}' src='{!! asset($d->{$col['name']}) !!}' width='50px' height='50px'/>
                                </a>
                            @else
                                <a data-label='{!! $filename !!}' href='{!! asset($d->{$col['name']}) !!}'>{!! $filename !!}</a>
                            @endif

                        @else
                            <span class='td-label'>
                                {!! $d->{$col['name']} !!}
                            </span>
                        @endif
                        <input type='hidden' name='{!! $name !!}-{!! $col['name'] !!}[]' value='{!! $d->{$col['name']} !!}'/>
                    </td>
                @endforeach
                <td>
                    <a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-warning btn-xs'>
                        <i class='fa fa-pencil'></i>
                    </a>
                    <a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-xs'>
                        <i class='fa fa-trash'></i>
                    </a>
                </td>
            </tr>

            @endforeach

            @if(count($data_child)==0)
                <tr class="trNull">
                    <td colspan="{{count($formInput['columns'])+1}}"
                        align="center">{{cbTrans('table_data_not_found')}}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>