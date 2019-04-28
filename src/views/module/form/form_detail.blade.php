@push('head')
    <style type="text/css">
        #table-detail tr td:first-child {
            font-weight: bold;
            width: 25%;
        }
    </style>
@endpush

<div class='table-responsive'>
    <table id='table-detail' class='table table-striped'>
        @foreach(module()->getColumnSingleton()->getDetailColumns() as $column)
        <tr>
            <th>{{ $column['label'] }}</th>
            <td>
                {!! getTypeHook($column['type'])->detailRender($row, $column) !!}
            </td>
        </tr>
        @endforeach
    </table>
</div>