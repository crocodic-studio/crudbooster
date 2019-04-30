@php $exist = []; @endphp
@foreach(module()->getColumnSingleton()->getAddEditColumns() as $index=>$column)
    <?php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */ ?>
    @if(!in_array($column->getType(), $exist))
        @if(file_exists(base_path('vendor/crocodicstudio/crudbooster/src/types/'.$column->getType().'/asset.blade.php')))
            @include('types::'.$column->getType().'.asset')
            @php $exist[] = $column->getType(); @endphp
        @endif
    @endif
@endforeach

<?php
    if(cb()->getCurrentMethod()=="getEdit") {
        /** @var $row object */
        columnSingleton()->valueAssignment($row);
    }
?>
@foreach(columnSingleton()->getAddEditColumns() as $index=>$column)
    @if( (cb()->getCurrentMethod()=="getAdd" && $column->getShowAdd()) || (cb()->getCurrentMethod()=="getEdit" && $column->getShowEdit()))
        @if(file_exists(base_path('vendor/crocodicstudio/crudbooster/src/types/'.$column->getType().'/component.blade.php')))
            @include('types::'.$column->getType().'.component')
        @else
            <p class='text-danger'>{{ $column->getType() }} is not found in type component system</p><br/>
        @endif
    @endif
@endforeach