@if($parent_table)
<div class="box box-default">
    <div class="box-body table-responsive no-padding">
        <table class='table table-bordered'>
            <tbody>

                <tr class='active'>
                    <td colspan="2">
                        <strong><i class='fa fa-bars'></i> {{ ucwords(urldecode(g('label'))) }}</strong>
                    </td>
                </tr>

                @foreach(explode(',',urldecode(g('parent_columns'))) as $column)
                    <tr>
                        <td width="25%">
                            <strong>
                                @if(urldecode(g('parent_columns_alias')))
                                    {{explode(',',urldecode(g('parent_columns_alias')))[$loop->index]}}
                                @else
                                    {{  ucwords(str_replace('_',' ',$column)) }}
                                @endif
                            </strong>
                        </td>
                        <td> {{ $parent_table->$column }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
@endif