@extends('crudbooster::layouts.layout')
@section('content')
    @push('head')
        <style type="text/css">
            #table-detail tr td:first-child {
                font-weight: bold;
                width: 25%;
            }
        </style>
    @endpush

    <p>
        <a href="{{ module()->url() }}"><i class="fa fa-arrow-left"></i> &nbsp; Back to list</a>
    </p>



    @if(isset($before_detail_form))
        @if(is_callable($before_detail_form))
            {!! call_user_func($before_detail_form, $row) !!}
        @elseif(is_string($before_detail_form))
            {!! $before_detail_form !!}
        @endif
     @endif


    <?php
    /** @var $row object */
    columnSingleton()->valueAssignment($row);
    $detailColumns = module()->getColumnSingleton()->getDetailColumns();
    ?>
    @if($detailColumns)
    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Form Detail</h1>
        </div>
        <div class="box-body">
            <div class='table-responsive'>
                <table id='table-detail' class='table table-striped'>
                    @foreach($detailColumns as $column)
                        <tr>
                            <th width="25%">{{ $column->getLabel() }}</th>
                            <td>
                                {!! getTypeHook($column->getType())->detailRender($row, $column) !!}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($after_detail_form))
        @if(is_callable($after_detail_form))
            {!! call_user_func($after_detail_form, $row) !!}
        @elseif(is_string($after_detail_form))
            {!! $after_detail_form !!}
        @endif
    @endif
@endsection