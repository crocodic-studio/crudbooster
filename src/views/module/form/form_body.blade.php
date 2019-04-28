@php $exist = []; @endphp
@foreach(module()->getColumnSingleton()->getAddEditColumns() as $column)
    <?php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */ ?>
    @if(!in_array($column->getType(), $exist))
        @if(file_exists(base_path('vendor/crocodicstudio/crudbooster/src/types/'.$column->getType().'/asset.blade.php')))
            @include('types::'.$column->getType().'.asset')
            @php $exist[] = $column->getType(); @endphp
        @endif
    @endif
@endforeach


@foreach(module()->getColumnSingleton()->getAddEditColumns() as $index=>$column)
    @if(file_exists(base_path('vendor/crocodicstudio/crudbooster/src/types/'.$column->getType().'/component.blade.php')))
        @include('types::'.$column->getType().'.component')
    @else
        <p class='text-danger'>{{ $column->getType() }} is not found in type component system</p><br/>
    @endif
@endforeach