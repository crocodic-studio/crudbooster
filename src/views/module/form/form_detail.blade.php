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

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Form Detail</h1>
        </div>
        <div class="box-body">
            <?php
            /** @var $row object */
            columnSingleton()->valueAssignment($row);
            ?>
            <div class='table-responsive'>
                <table id='table-detail' class='table table-striped'>
                    @foreach(module()->getColumnSingleton()->getDetailColumns() as $column)
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
@endsection