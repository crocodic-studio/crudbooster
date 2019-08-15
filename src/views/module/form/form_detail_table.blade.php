<table id='table-detail' class='table table-striped'>
    @foreach($detailColumns as $column)
        <tr>
            <th width="25%">{{ $column->getLabel() }}</th>
            <td>
                <?php
                /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */
                $value = getTypeHook($column->getType())->detailRender($row, $column);
                $value = call_user_func($column->getDetailDisplayTransform(), $value, $row);
                echo $value;
                ?>
            </td>
        </tr>
    @endforeach
</table>