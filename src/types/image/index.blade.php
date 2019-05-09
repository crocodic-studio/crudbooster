@php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */  @endphp
@if($column->getValue())
    <div align="center">
        <a data-lightbox="preview-image" title="Preview Image" href="{{ ($column->getValue())?asset($column->getValue()):"javascript:;" }}">
            <img src="{{ ($column->getValue())?asset($column->getValue()):cbAsset("images/no_image_100x100.png") }}" style="height: 50px" alt="{{ $column->getLabel() }}">
        </a>
    </div>
@else
    <div align="center">
        <img title="Image Not Available" src="{{ cbAsset("images/no_image_100x100.png") }}" style="height: 50px" alt="Image Not Available">
    </div>
@endif